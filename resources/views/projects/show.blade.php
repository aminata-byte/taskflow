@extends('layouts.app')
@section('title', $project->title)

@section('content')
    <div class="page-container" style="max-width:100%;">

        <div class="kanban-header">
            <div>
                <h1 class="page-title">{{ $project->title }}</h1>
                @if ($project->description)
                    <p class="page-subtitle">{{ $project->description }}</p>
                @endif
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary"> Modifier</a>
                <a href="{{ route('projects.index') }}" class="btn-secondary"> Retour</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" style="max-width:500px;">{{ session('success') }}</div>
        @endif

        @php
            $allTasks = $project->columns->flatMap->tasks;
            $total = $allTasks->count();
            $done = $project->columns->where('name', 'Terminé')->flatMap->tasks->count();
            $inprog = $project->columns->where('name', 'En cours')->flatMap->tasks->count();
            $todo = $project->columns->where('name', 'À faire')->flatMap->tasks->count();
            $unassigned = $allTasks->whereNull('assigned_to')->count();
            $late = $project->columns
                ->where('name', '!=', 'Terminé')
                ->flatMap->tasks->filter(fn($t) => $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast())
                ->count();
            $progress = $total > 0 ? round(($done / $total) * 100) : 0;
            $team = $project->teams->first();
            $isPersonal = !$team && !Auth::user()->isAdmin();
        @endphp

        {{-- STATS --}}
        <div class="card" style="margin-bottom:1.5rem; padding:1.5rem 2rem;">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:1rem;">
                <div style="display:flex; gap:2rem; flex-wrap:wrap;">
                    <div style="text-align:center; cursor:pointer;" onclick="filterTasks('all')">
                        <div style="font-size:1.5rem; font-weight:800; color:var(--accent-1);">{{ $total }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total</div>
                    </div>
                    <div style="text-align:center; cursor:pointer;" onclick="filterTasks('À faire')">
                        <div style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">{{ $todo }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">À faire</div>
                    </div>
                    <div style="text-align:center; cursor:pointer;" onclick="filterTasks('En cours')">
                        <div style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">{{ $inprog }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">En cours</div>
                    </div>
                    <div style="text-align:center; cursor:pointer;" onclick="filterTasks('Terminé')">
                        <div style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">{{ $done }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Terminé</div>
                    </div>
                    <div style="text-align:center; cursor:pointer;" onclick="filterTasks('retard')">
                        <div style="font-size:1.5rem; font-weight:800; color:#EF4444;">{{ $late }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">En retard</div>
                    </div>
                    @if (!$isPersonal)
                        <div style="text-align:center; cursor:pointer;" onclick="filterTasks('unassigned')">
                            <div style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">{{ $unassigned }}
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Non assigné
                            </div>
                        </div>
                    @endif
                </div>
                <div style="font-size:2rem; font-weight:800; color:var(--accent-1);">{{ $progress }}%</div>
            </div>
            <div style="background:rgba(255,255,255,0.05); border-radius:20px; height:10px; overflow:hidden;">
                <div
                    style="background:var(--accent-grad); height:100%; width:{{ $progress }}%; border-radius:20px; transition:width 0.5s;">
                </div>
            </div>
        </div>

        {{-- ONGLETS (admin/équipe seulement) --}}
        @if (!$isPersonal)
            <div style="display:flex; gap:10px; margin-bottom:1.5rem;">
                <button onclick="showTab('taches')" id="tab-taches"
                    style="padding:10px 28px; border-radius:30px; border:2px solid rgba(99,102,241,0.5); background:var(--accent-grad); color:white; font-weight:700; font-size:0.95rem; cursor:pointer;">
                    Tâches
                </button>
                <button onclick="showTab('membres')" id="tab-membres"
                    style="padding:10px 28px; border-radius:30px; border:2px solid var(--border); background:transparent; color:var(--text-secondary); font-weight:700; font-size:0.95rem; cursor:pointer;">
                    Membres
                </button>
            </div>
        @endif

        {{-- ===== TÂCHES ===== --}}
        <div id="panel-taches">

            {{-- Filtres --}}
            <div style="display:flex; gap:8px; margin-bottom:1.2rem; flex-wrap:wrap; align-items:center;">
                <span style="font-size:0.82rem; color:var(--text-muted); margin-right:4px;">Filtrer :</span>
                <button onclick="filterTasks('all')" data-filter="all" class="filter-btn"
                    style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:var(--accent-grad); color:white; font-size:0.82rem; font-weight:600; cursor:pointer;">
                    Tous ({{ $total }})
                </button>
                <button onclick="filterTasks('À faire')" data-filter="À faire" class="filter-btn"
                    style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:rgba(255,255,255,0.05); color:var(--text-secondary); font-size:0.82rem; font-weight:600; cursor:pointer;">
                    À faire ({{ $todo }})
                </button>
                <button onclick="filterTasks('En cours')" data-filter="En cours" class="filter-btn"
                    style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:rgba(255,255,255,0.05); color:var(--text-secondary); font-size:0.82rem; font-weight:600; cursor:pointer;">
                    En cours ({{ $inprog }})
                </button>
                <button onclick="filterTasks('Terminé')" data-filter="Terminé" class="filter-btn"
                    style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:rgba(255,255,255,0.05); color:var(--text-secondary); font-size:0.82rem; font-weight:600; cursor:pointer;">
                    Terminé ({{ $done }})
                </button>
                <button onclick="filterTasks('retard')" data-filter="retard" class="filter-btn"
                    style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:rgba(255,255,255,0.05); color:var(--text-secondary); font-size:0.82rem; font-weight:600; cursor:pointer;">
                    En retard ({{ $late }})
                </button>
                @if (!$isPersonal)
                    <button onclick="filterTasks('unassigned')" data-filter="unassigned" class="filter-btn"
                        style="padding:5px 16px; border-radius:20px; border:1px solid var(--border); background:rgba(255,255,255,0.05); color:var(--text-secondary); font-size:0.82rem; font-weight:600; cursor:pointer;">
                        Non assigné ({{ $unassigned }})
                    </button>
                @endif
            </div>

            {{-- Formulaire ajout rapide --}}
            <div class="card" style="margin-bottom:1.5rem; padding:1.2rem 1.5rem;">
                <h3
                    style="font-family:'Sora',sans-serif; font-size:0.95rem; font-weight:700; margin-bottom:1rem; color:var(--text-secondary);">
                    ➕ Ajouter une tâche
                </h3>
                <form action="{{ route('columns.tasks.store', $project->columns->where('name', 'À faire')->first()) }}"
                    method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
                    @csrf
                    <input type="text" name="title" placeholder="Titre de la tâche..." required
                        style="flex:2; min-width:180px; background:var(--bg-column); border:1px solid var(--border); border-radius:10px; padding:10px 14px; color:var(--text-primary); font-size:0.9rem;">
                    <select name="priority"
                        style="flex:1; min-width:140px; background:var(--bg-column); border:1px solid var(--border); border-radius:10px; padding:10px 14px; color:var(--text-primary); font-size:0.9rem;">
                        <option value="basse">Basse</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="haute">Haute</option>
                    </select>
                    <input type="date" name="due_date"
                        style="flex:1; min-width:140px; background:var(--bg-column); border:1px solid var(--border); border-radius:10px; padding:10px 14px; color:var(--text-muted); font-size:0.9rem;">
                    <button type="submit" class="btn-primary" style="padding:10px 20px;">＋ Ajouter</button>
                </form>
            </div>

            {{-- Kanban drag & drop (espace personnel uniquement) --}}
            @if ($isPersonal)
                <div
                    style="display:grid; grid-template-columns: repeat({{ $project->columns->count() }}, 1fr); gap:1rem; margin-bottom:1.5rem;">
                    @foreach ($project->columns as $column)
                        <div class="kanban-column" data-column-id="{{ $column->id }}" ondragover="allowDrop(event)"
                            ondrop="dropTaskPersonal(event, {{ $column->id }})"
                            ondragenter="this.style.background='rgba(99,102,241,0.08)'"
                            ondragleave="this.style.background=''">
                            <div class="column-header">
                                <span class="column-title">{{ $column->name }}</span>
                                <span class="column-count"
                                    id="pcount-{{ $column->id }}">{{ $column->tasks->count() }}</span>
                            </div>
                            <div class="tasks-list" id="plist-{{ $column->id }}" style="min-height:80px;">
                                @forelse($column->tasks as $task)
                                    <div class="task-card" draggable="true" data-task-id="{{ $task->id }}"
                                        ondragstart="dragStartPersonal(event, {{ $task->id }})"
                                        ondragend="this.style.opacity='1'">
                                        <div class="task-title">{{ $task->title }}</div>
                                        @if ($task->description)
                                            <div class="task-desc">{{ Str::limit($task->description, 50) }}</div>
                                        @endif
                                        <div
                                            style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                            @if ($task->due_date)
                                                @php $isLate = \Carbon\Carbon::parse($task->due_date)->isPast() && $column->name !== 'Terminé'; @endphp
                                                <span
                                                    style="font-size:0.75rem; color:{{ $isLate ? '#F87171' : 'var(--text-muted)' }}">
                                                    {{ $isLate ? '🔥' : '' }}
                                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span></span>
                                            @endif
                                            @if ($task->priority)
                                                <span
                                                    class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-col-msg"
                                        style="color:var(--text-muted); font-size:0.8rem; text-align:center; padding:1rem 0.5rem;">
                                        Aucune tâche
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tableau des tâches --}}
            <div class="card" style="padding:0; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse;" id="tasks-table">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background:rgba(255,255,255,0.02);">
                            <th
                                style="text-align:left; padding:14px 20px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Tâche</th>
                            @if (!$isPersonal)
                                <th
                                    style="text-align:left; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                    Assigné à</th>
                            @endif
                            <th
                                style="text-align:center; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                État</th>
                            <th
                                style="text-align:center; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Priorité</th>
                            <th
                                style="text-align:center; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Échéance</th>
                            <th
                                style="text-align:center; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allTasks as $task)
                            @php
                                $colName = $task->column?->name ?? '?';
                                $isOverdue =
                                    $task->due_date &&
                                    \Carbon\Carbon::parse($task->due_date)->isPast() &&
                                    $colName !== 'Terminé';
                                $isUnassigned = is_null($task->assigned_to);
                                $stateColor = match ($colName) {
                                    'Terminé' => '#10B981',
                                    'En cours' => '#F59E0B',
                                    default => '#6366F1',
                                };
                                $stateBg = match ($colName) {
                                    'Terminé' => 'rgba(16,185,129,0.12)',
                                    'En cours' => 'rgba(245,158,11,0.12)',
                                    default => 'rgba(99,102,241,0.12)',
                                };
                                $prioColor = match ($task->priority ?? 'basse') {
                                    'haute' => '#EF4444',
                                    'moyenne' => '#F59E0B',
                                    default => '#10B981',
                                };
                                $prioBg = match ($task->priority ?? 'basse') {
                                    'haute' => 'rgba(239,68,68,0.12)',
                                    'moyenne' => 'rgba(245,158,11,0.12)',
                                    default => 'rgba(16,185,129,0.12)',
                                };
                                $col = $task->column;
                            @endphp
                            <tr class="task-row" data-state="{{ $colName }}" data-task-id="{{ $task->id }}"
                                data-overdue="{{ $isOverdue ? '1' : '0' }}"
                                data-unassigned="{{ $isUnassigned ? '1' : '0' }}"
                                style="border-bottom:1px solid var(--border); transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(99,102,241,0.04)'"
                                onmouseout="this.style.background='transparent'">

                                <td style="padding:14px 20px;">
                                    <div style="font-weight:600; font-size:0.9rem; color:var(--text-primary);">
                                        {{ $task->title }}</div>
                                    @if ($task->description)
                                        <div style="color:var(--text-muted); font-size:0.78rem; margin-top:2px;">
                                            {{ Str::limit($task->description, 60) }}</div>
                                    @endif
                                    @if ($isOverdue)
                                        <span style="color:#F87171; font-size:0.72rem; font-weight:700;">🔥 En
                                            retard</span>
                                    @endif
                                </td>

                                @if (!$isPersonal)
                                    <td style="padding:10px 16px;">
                                        @if (Auth::user()->isAdmin() && $team)
                                            <select onchange="assignTask({{ $task->id }}, this.value, this)"
                                                style="background:var(--bg-column); border:1px solid var(--border); border-radius:8px; padding:5px 10px; color:var(--text-primary); font-size:0.82rem; cursor:pointer; min-width:130px;">
                                                <option value="">— Choisir —</option>
                                                @foreach ($team->members as $m)
                                                    <option value="{{ $m->id }}"
                                                        {{ $task->assigned_to == $m->id ? 'selected' : '' }}>
                                                        {{ $m->name }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($task->assignedUser)
                                            <div style="display:flex; align-items:center; gap:7px;">
                                                <div
                                                    style="width:26px; height:26px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; flex-shrink:0; color:white;">
                                                    {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                                </div>
                                                <span
                                                    style="font-size:0.85rem; font-weight:600; color:var(--accent-1);">{{ $task->assignedUser->name }}</span>
                                            </div>
                                        @else
                                            <span style="color:#FBBF24; font-size:0.82rem; font-weight:600;">⚠️ Non
                                                assigné</span>
                                        @endif
                                    </td>
                                @endif

                                <td style="padding:14px 16px; text-align:center;">
                                    <span class="state-badge-{{ $task->id }}"
                                        style="background:{{ $stateBg }}; color:{{ $stateColor }}; padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700; white-space:nowrap;">
                                        {{ $colName }}
                                    </span>
                                </td>

                                <td style="padding:14px 16px; text-align:center;">
                                    <span
                                        style="background:{{ $prioBg }}; color:{{ $prioColor }}; padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                        {{ ucfirst($task->priority ?? 'basse') }}
                                    </span>
                                </td>

                                <td
                                    style="padding:14px 16px; text-align:center; font-size:0.82rem; color:{{ $isOverdue ? '#F87171' : 'var(--text-secondary)' }}; font-weight:{{ $isOverdue ? '700' : '400' }};">
                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : '—' }}
                                </td>

                                <td style="padding:14px 16px; text-align:center;">
                                    <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                                        @if ($col)
                                            <a href="{{ route('columns.tasks.edit', [$col, $task]) }}"
                                                class="task-btn edit" title="Modifier">✏️</a>
                                            <form action="{{ route('columns.tasks.destroy', [$col, $task]) }}"
                                                method="POST" onsubmit="return confirm('Supprimer cette tâche ?')"
                                                style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="task-btn delete"
                                                    title="Supprimer">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isPersonal ? 5 : 6 }}"
                                    style="text-align:center; padding:3rem; color:var(--text-muted);">
                                    <div style="font-size:2rem; margin-bottom:0.5rem;">📭</div>
                                    Aucune tâche pour ce projet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="no-filter-result"
                    style="display:none; text-align:center; padding:3rem; color:var(--text-muted);">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">🔍</div>
                    Aucune tâche dans cette catégorie
                </div>
            </div>
        </div>

        {{-- ===== MEMBRES (admin/équipe seulement) ===== --}}
        @if (!$isPersonal)
            <div id="panel-membres" style="display:none;">
                @if (!$team)
                    <div class="empty-state">
                        <span class="empty-icon">👥</span>
                        <h3 class="empty-title">Aucune équipe assignée à ce projet</h3>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.teams.create') }}" class="btn-primary">+ Créer une équipe</a>
                        @endif
                    </div>
                @else
                    @php $unassignedTasks = $allTasks->whereNull('assigned_to')->values(); @endphp
                    @if ($unassignedTasks->isNotEmpty())
                        <div class="card" style="margin-bottom:1.5rem; border:1px solid rgba(245,158,11,0.3);">
                            <h3
                                style="font-family:'Sora',sans-serif; font-size:1rem; font-weight:700; color:#FBBF24; margin-bottom:1rem;">
                                ⚠️ {{ $unassignedTasks->count() }} tâche(s) non assignée(s)
                            </h3>
                            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                @foreach ($unassignedTasks as $task)
                                    <span
                                        style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#FBBF24; padding:4px 12px; border-radius:20px; font-size:0.82rem;">{{ $task->title }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:1.5rem;">
                        @foreach ($team->members as $member)
                            @php
                                $memberTasks = $member->assignedTasks->filter(
                                    fn($t) => $t->column && $project->columns->pluck('id')->contains($t->column_id),
                                );
                                $memberDone = $memberTasks->filter(fn($t) => $t->column?->name === 'Terminé')->count();
                                $memberInProg = $memberTasks
                                    ->filter(fn($t) => $t->column?->name === 'En cours')
                                    ->count();
                                $memberTodo = $memberTasks->filter(fn($t) => $t->column?->name === 'À faire')->count();
                                $memberTotal = $memberTasks->count();
                                $memberPct = $memberTotal > 0 ? round(($memberDone / $memberTotal) * 100) : 0;
                                $memberLate = $memberTasks
                                    ->filter(
                                        fn($t) => $t->column?->name !== 'Terminé' &&
                                            $t->due_date &&
                                            \Carbon\Carbon::parse($t->due_date)->isPast(),
                                    )
                                    ->count();
                            @endphp
                            <div class="card" style="padding:1.5rem;">
                                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                                    <div
                                        style="width:44px; height:44px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; flex-shrink:0; color:white;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700; font-size:1rem;">{{ $member->name }}</div>
                                        <div style="color:var(--text-muted); font-size:0.78rem;">{{ $member->email }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display:flex; gap:8px; margin-bottom:1rem; flex-wrap:wrap;">
                                    <span
                                        style="background:rgba(99,102,241,0.15); color:#818CF8; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">📋
                                        {{ $memberTodo }}</span>
                                    <span
                                        style="background:rgba(245,158,11,0.15); color:#FBBF24; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">⚡
                                        {{ $memberInProg }}</span>
                                    <span
                                        style="background:rgba(16,185,129,0.15); color:#34D399; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">✅
                                        {{ $memberDone }}</span>
                                    @if ($memberLate > 0)
                                        <span
                                            style="background:rgba(239,68,68,0.15); color:#F87171; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">🔥
                                            {{ $memberLate }}</span>
                                    @endif
                                </div>
                                <div
                                    style="background:rgba(255,255,255,0.05); border-radius:20px; height:6px; overflow:hidden; margin-bottom:4px;">
                                    <div
                                        style="background:var(--accent-grad); height:100%; width:{{ $memberPct }}%; border-radius:20px;">
                                    </div>
                                </div>
                                <div style="color:var(--text-muted); font-size:0.75rem; margin-bottom:1.2rem;">
                                    {{ $memberPct }}% complété</div>
                                @if ($memberTasks->isNotEmpty())
                                    <div style="margin-bottom:1.2rem;">
                                        <div
                                            style="font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:8px; text-transform:uppercase;">
                                            Tâches assignées</div>
                                        @foreach ($memberTasks as $task)
                                            @php
                                                $tState = $task->column?->name ?? '?';
                                                $tColor = match ($tState) {
                                                    'Terminé' => '#10B981',
                                                    'En cours' => '#F59E0B',
                                                    default => '#6366F1',
                                                };
                                                $tLate =
                                                    $task->column?->name !== 'Terminé' &&
                                                    $task->due_date &&
                                                    \Carbon\Carbon::parse($task->due_date)->isPast();
                                            @endphp
                                            <div
                                                style="display:flex; justify-content:space-between; align-items:center; padding:7px 10px; background:rgba(255,255,255,0.03); border-radius:8px; margin-bottom:5px; border:1px solid var(--border);">
                                                <span
                                                    style="font-size:0.85rem; font-weight:500;">{{ $task->title }}</span>
                                                <div style="display:flex; gap:5px; align-items:center; flex-shrink:0;">
                                                    @if ($tLate)
                                                        <span style="color:#F87171; font-size:0.72rem;">🔥</span>
                                                    @endif
                                                    <span
                                                        style="color:{{ $tColor }}; font-size:0.72rem; font-weight:700; white-space:nowrap;">{{ $tState }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if (Auth::user()->isAdmin() && $unassignedTasks->isNotEmpty())
                                    <form action="{{ route('admin.teams.assign-task') }}" method="POST"
                                        style="display:flex; gap:8px; align-items:center;">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <select name="task_id" class="form-control"
                                            style="flex:1; padding:8px 12px; font-size:0.82rem;" required>
                                            <option value="">-- Assigner une tâche --</option>
                                            @foreach ($unassignedTasks as $ut)
                                                <option value="{{ $ut->id }}">{{ $ut->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-primary"
                                            style="padding:8px 14px; white-space:nowrap;">✓</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        // ===== ONGLETS =====
        function showTab(tab) {
            document.getElementById('panel-taches').style.display = tab === 'taches' ? 'block' : 'none';
            document.getElementById('panel-membres').style.display = tab === 'membres' ? 'block' : 'none';
            const active =
                'padding:10px 28px; border-radius:30px; border:2px solid rgba(99,102,241,0.5); background:var(--accent-grad); color:white; font-weight:700; font-size:0.95rem; cursor:pointer;';
            const inactive =
                'padding:10px 28px; border-radius:30px; border:2px solid var(--border); background:transparent; color:var(--text-secondary); font-weight:700; font-size:0.95rem; cursor:pointer;';
            document.getElementById('tab-taches').style.cssText = tab === 'taches' ? active : inactive;
            document.getElementById('tab-membres').style.cssText = tab === 'membres' ? active : inactive;
        }

        // ===== FILTRES =====
        function filterTasks(filter) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const isActive = btn.dataset.filter === filter;
                btn.style.background = isActive ? 'var(--accent-grad)' : 'rgba(255,255,255,0.05)';
                btn.style.color = isActive ? 'white' : 'var(--text-secondary)';
            });
            const rows = document.querySelectorAll('.task-row');
            let visible = 0;
            rows.forEach(row => {
                const show = filter === 'all' ? true :
                    filter === 'retard' ? row.dataset.overdue === '1' :
                    filter === 'unassigned' ? row.dataset.unassigned === '1' :
                    row.dataset.state === filter;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('no-filter-result').style.display = visible === 0 ? 'block' : 'none';
        }

        // ===== DRAG & DROP PERSONNEL =====
        let draggedPersonalTaskId = null;

        function allowDrop(e) {
            e.preventDefault();
        }

        function dragStartPersonal(e, taskId) {
            draggedPersonalTaskId = taskId;
            e.target.style.opacity = '0.4';
            e.dataTransfer.effectAllowed = 'move';
        }

        function dropTaskPersonal(e, columnId) {
            e.preventDefault();
            document.querySelectorAll('.kanban-column').forEach(c => c.style.background = '');
            if (!draggedPersonalTaskId) return;

            const taskEl = document.querySelector(`.task-card[data-task-id="${draggedPersonalTaskId}"]`);
            const targetList = document.getElementById(`plist-${columnId}`);
            if (!taskEl || !targetList || taskEl.parentElement === targetList) return;

            const sourceList = taskEl.parentElement;
            const targetColName = targetList.closest('.kanban-column').querySelector('.column-title').textContent.trim();
            const srcColId = sourceList.id.replace('plist-', '');

            // Déplacement immédiat
            targetList.appendChild(taskEl);
            taskEl.style.opacity = '1';
            showOrHideEmpty(targetList);
            showOrHideEmpty(sourceList);
            updatePCount(columnId);
            updatePCount(srcColId);

            // Sync tableau + stats
            updateTableRow(draggedPersonalTaskId, targetColName);
            updateStatsBar();

            const savedId = draggedPersonalTaskId;
            draggedPersonalTaskId = null;

            fetch('{{ route('projects.move-task') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        task_id: savedId,
                        column_id: columnId
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (!d.success) location.reload();
                })
                .catch(() => location.reload());
        }

        function updateTableRow(taskId, newColName) {
            document.querySelectorAll('.task-row').forEach(row => {
                if (row.dataset.taskId == taskId) {
                    row.dataset.state = newColName;
                    const stateColor = newColName === 'Terminé' ? '#10B981' : newColName === 'En cours' ?
                        '#F59E0B' : '#6366F1';
                    const stateBg = newColName === 'Terminé' ? 'rgba(16,185,129,0.12)' : newColName === 'En cours' ?
                        'rgba(245,158,11,0.12)' : 'rgba(99,102,241,0.12)';
                    const badge = row.querySelector(`.state-badge-${taskId}`);
                    if (badge) {
                        badge.textContent = newColName;
                        badge.style.background = stateBg;
                        badge.style.color = stateColor;
                    }
                }
            });
        }

        function updateStatsBar() {
            let todo = 0,
                inprog = 0,
                done = 0;
            document.querySelectorAll('.task-row').forEach(row => {
                const s = row.dataset.state;
                if (s === 'Terminé') done++;
                else if (s === 'En cours') inprog++;
                else todo++;
            });
            const total = todo + inprog + done;
            const progress = total > 0 ? Math.round((done / total) * 100) : 0;

            document.querySelectorAll('[onclick]').forEach(el => {
                const fn = el.getAttribute('onclick') || '';
                const val = el.querySelector('div:first-child');
                if (!val) return;
                if (fn.includes("'À faire'")) val.textContent = todo;
                if (fn.includes("'En cours'")) val.textContent = inprog;
                if (fn.includes("'Terminé'")) val.textContent = done;
                if (fn.includes("'all'")) val.textContent = total;
            });
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const f = btn.dataset.filter;
                const counts = {
                    'all': total,
                    'À faire': todo,
                    'En cours': inprog,
                    'Terminé': done
                };
                if (counts[f] !== undefined) btn.textContent = btn.textContent.replace(/\d+/, counts[f]);
            });

            const bar = document.querySelector('[style*="transition:width 0.5s"]');
            if (bar) bar.style.width = progress + '%';
            document.querySelectorAll('[style*="font-size:2rem"]').forEach(el => {
                if (el.textContent.includes('%')) el.textContent = progress + '%';
            });
        }

        function showOrHideEmpty(listEl) {
            if (!listEl) return;
            const hasTasks = listEl.querySelectorAll('.task-card').length > 0;
            const existing = listEl.querySelector('.empty-col-msg');
            if (hasTasks) {
                if (existing) existing.remove();
            } else if (!existing) {
                const div = document.createElement('div');
                div.className = 'empty-col-msg';
                div.style.cssText = 'color:var(--text-muted); font-size:0.8rem; text-align:center; padding:1rem 0.5rem;';
                div.textContent = 'Aucune tâche';
                listEl.appendChild(div);
            }
        }

        function updatePCount(columnId) {
            const list = document.getElementById(`plist-${columnId}`);
            const badge = document.getElementById(`pcount-${columnId}`);
            if (list && badge) badge.textContent = list.querySelectorAll('.task-card').length;
        }

        // ===== ASSIGN TASK (admin) =====
        function assignTask(taskId, userId, selectEl) {
            if (!userId) return;
            selectEl.style.opacity = '0.5';
            selectEl.disabled = true;
            fetch('{{ route('admin.teams.assign-task') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        user_id: userId
                    })
                })
                .then(r => r.json())
                .then(() => {
                    selectEl.style.opacity = '1';
                    selectEl.disabled = false;
                    selectEl.style.border = '1px solid #10B981';
                    const row = selectEl.closest('tr');
                    if (row) row.dataset.unassigned = '0';
                    setTimeout(() => {
                        selectEl.style.border = '1px solid var(--border)';
                    }, 1500);
                })
                .catch(() => {
                    selectEl.style.opacity = '1';
                    selectEl.disabled = false;
                    selectEl.style.border = '1px solid #EF4444';
                });
        }
    </script>
@endpush
