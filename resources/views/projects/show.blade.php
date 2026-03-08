@extends('layouts.app')
@section('title', $project->title)

@section('content')
    <div class="page-container" style="max-width:100%;">

        <div class="breadcrumb">
            <a href="{{ route('projects.index') }}">Projets</a>
            <span class="separator">›</span>
            <span>{{ $project->title }}</span>
        </div>

        <div class="kanban-header">
            <div>
                <h1 class="page-title">{{ $project->title }}</h1>
                @if ($project->description)
                    <p class="page-subtitle">{{ $project->description }}</p>
                @endif
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">✏️ Modifier</a>
                <a href="{{ route('projects.index') }}" class="btn-secondary">← Retour</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" style="max-width:500px;">✅ {{ session('success') }}</div>
        @endif

        @php
            $allTasks = $project->columns->flatMap->tasks;
            $total = $allTasks->count();
            $done = $project->columns->where('name', 'Terminé')->flatMap->tasks->count();
            $inprog = $project->columns->where('name', 'En cours')->flatMap->tasks->count();
            $todo = $project->columns->where('name', 'À faire')->flatMap->tasks->count();
            $late = $project->columns
                ->where('name', '!=', 'Terminé')
                ->flatMap->tasks->filter(fn($t) => $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast())
                ->count();
            $progress = $total > 0 ? round(($done / $total) * 100) : 0;
            $team = $project->teams->first();
        @endphp

        {{-- STATS --}}
        <div class="card" style="margin-bottom:1.5rem; padding:1.5rem 2rem;">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:1rem;">
                <div style="display:flex; gap:2rem; flex-wrap:wrap;">
                    <div style="text-align:center;">
                        <div style="font-size:1.5rem; font-weight:800; color:var(--accent-1);">{{ $total }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:1.5rem; font-weight:800; color:#6366F1;">{{ $todo }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">À faire</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:1.5rem; font-weight:800; color:#F59E0B;">{{ $inprog }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">En cours</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:1.5rem; font-weight:800; color:#10B981;">{{ $done }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Terminé</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:1.5rem; font-weight:800; color:#EF4444;">{{ $late }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">En retard</div>
                    </div>
                </div>
                <div style="font-size:2rem; font-weight:800; color:var(--accent-1);">{{ $progress }}%</div>
            </div>
            <div style="background:rgba(255,255,255,0.05); border-radius:20px; height:10px; overflow:hidden;">
                <div
                    style="background:var(--accent-grad); height:100%; width:{{ $progress }}%; border-radius:20px; transition:width 0.5s;">
                </div>
            </div>
        </div>

        {{-- ONGLETS --}}
        <div style="display:flex; gap:10px; margin-bottom:1.5rem;">
            <button onclick="showTab('taches')" id="tab-taches"
                style="padding:10px 28px; border-radius:30px; border:2px solid rgba(99,102,241,0.5); background:var(--accent-grad); color:white; font-weight:700; font-size:0.95rem; cursor:pointer;">
                📋 Tâches
            </button>
            <button onclick="showTab('membres')" id="tab-membres"
                style="padding:10px 28px; border-radius:30px; border:2px solid var(--border); background:transparent; color:var(--text-secondary); font-weight:700; font-size:0.95rem; cursor:pointer;">
                👥 Membres
            </button>
        </div>

        {{-- ===== ONGLET TÂCHES ===== --}}
        <div id="panel-taches">

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
                        <option value="basse">🟢 Basse</option>
                        <option value="moyenne">🟡 Moyenne</option>
                        <option value="haute">🔴 Haute</option>
                    </select>
                    <input type="date" name="due_date"
                        style="flex:1; min-width:140px; background:var(--bg-column); border:1px solid var(--border); border-radius:10px; padding:10px 14px; color:var(--text-muted); font-size:0.9rem;">
                    <button type="submit" class="btn-primary" style="padding:10px 20px;">＋ Ajouter</button>
                </form>
            </div>

            {{-- Liste des tâches --}}
            <div class="card" style="padding:0; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background:rgba(255,255,255,0.02);">
                            <th
                                style="text-align:left; padding:14px 20px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Tâche</th>
                            <th
                                style="text-align:left; padding:14px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                Assigné à</th>
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
                                $stateIcon = match ($colName) {
                                    'Terminé' => '✅',
                                    'En cours' => '⚡',
                                    default => '📋',
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
                                $isOverdue =
                                    $task->due_date &&
                                    \Carbon\Carbon::parse($task->due_date)->isPast() &&
                                    $colName !== 'Terminé';
                                $col = $task->column;
                            @endphp
                            <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(99,102,241,0.04)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Titre --}}
                                <td style="padding:14px 20px;">
                                    <div style="font-weight:600; font-size:0.9rem; color:var(--text-primary);">
                                        {{ $task->title }}</div>
                                    @if ($task->description)
                                        <div style="color:var(--text-muted); font-size:0.78rem; margin-top:2px;">
                                            {{ Str::limit($task->description, 60) }}</div>
                                    @endif
                                    @if ($isOverdue)
                                        <span style="color:#F87171; font-size:0.72rem; font-weight:700;">🔥 En retard</span>
                                    @endif
                                </td>

                                {{-- Assigné --}}
                                <td style="padding:14px 16px;">
                                    @if ($task->assignedUser)
                                        <div style="display:flex; align-items:center; gap:7px;">
                                            <div
                                                style="width:26px; height:26px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; flex-shrink:0;">
                                                {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                            </div>
                                            <span
                                                style="font-size:0.85rem; font-weight:600; color:var(--accent-1);">{{ $task->assignedUser->name }}</span>
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted); font-size:0.82rem;">— Non assigné</span>
                                    @endif
                                </td>

                                {{-- État --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <span
                                        style="background:{{ $stateBg }}; color:{{ $stateColor }}; padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700; white-space:nowrap;">
                                        {{ $stateIcon }} {{ $colName }}
                                    </span>
                                </td>

                                {{-- Priorité --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <span
                                        style="background:{{ $prioBg }}; color:{{ $prioColor }}; padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                        {{ ucfirst($task->priority ?? 'basse') }}
                                    </span>
                                </td>

                                {{-- Échéance --}}
                                <td
                                    style="padding:14px 16px; text-align:center; font-size:0.82rem; color:{{ $isOverdue ? '#F87171' : 'var(--text-secondary)' }}; font-weight:{{ $isOverdue ? '700' : '400' }};">
                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Actions --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <div
                                        style="display:flex; gap:6px; justify-content:center; align-items:center; flex-wrap:wrap;">
                                        {{-- Bouton assigner (admin uniquement, si équipe existe) --}}
                                        @if (Auth::user()->isAdmin() && $team)
                                            <button
                                                onclick="openAssign({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                                style="background:rgba(99,102,241,0.15); color:#818CF8; border:1px solid rgba(99,102,241,0.3); padding:4px 10px; border-radius:8px; font-size:0.78rem; cursor:pointer; white-space:nowrap;">
                                                👤 Assigner
                                            </button>
                                        @endif
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
                                <td colspan="6" style="text-align:center; padding:3rem; color:var(--text-muted);">
                                    <div style="font-size:2rem; margin-bottom:0.5rem;">📭</div>
                                    Aucune tâche pour ce projet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ONGLET MEMBRES ===== --}}
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
                                    style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#FBBF24; padding:4px 12px; border-radius:20px; font-size:0.82rem;">
                                    📋 {{ $task->title }}
                                </span>
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
                            $memberInProg = $memberTasks->filter(fn($t) => $t->column?->name === 'En cours')->count();
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
                                    style="width:44px; height:44px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; flex-shrink:0;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:1rem;">{{ $member->name }}</div>
                                    <div style="color:var(--text-muted); font-size:0.78rem;">{{ $member->email }}</div>
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
                                            <span style="font-size:0.85rem; font-weight:500;">{{ $task->title }}</span>
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
                                        style="padding:8px 14px; white-space:nowrap;">✅</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- MODAL ASSIGNATION (onglet Tâches) --}}
    @if (Auth::user()->isAdmin() && $team)
        <div id="assignModal"
            style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
            <div
                style="background:var(--bg-card); border-radius:16px; padding:2rem; min-width:350px; border:1px solid var(--border); box-shadow:0 20px 60px rgba(0,0,0,0.5);">
                <h3 style="font-family:'Sora',sans-serif; font-weight:700; margin-bottom:0.5rem;">👤 Assigner la tâche</h3>
                <p id="assignTaskTitle" style="color:var(--text-secondary); font-size:0.875rem; margin-bottom:1.5rem;">
                </p>
                <form action="{{ route('admin.teams.assign-task') }}" method="POST">
                    @csrf
                    <input type="hidden" name="task_id" id="assignTaskId">
                    <div class="form-group">
                        <label class="form-label">Choisir un membre</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach ($team->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:1.5rem;">
                        <button type="submit" class="btn-primary"> Assigner</button>
                        <button type="button" onclick="closeAssign()" class="btn-secondary">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function showTab(tab) {
            document.getElementById('panel-taches').style.display = tab === 'taches' ? 'block' : 'none';
            document.getElementById('panel-membres').style.display = tab === 'membres' ? 'block' : 'none';
            const btnT = document.getElementById('tab-taches');
            const btnM = document.getElementById('tab-membres');
            if (tab === 'taches') {
                btnT.style.cssText =
                    'padding:10px 28px; border-radius:30px; border:2px solid rgba(99,102,241,0.5); background:var(--accent-grad); color:white; font-weight:700; font-size:0.95rem; cursor:pointer;';
                btnM.style.cssText =
                    'padding:10px 28px; border-radius:30px; border:2px solid var(--border); background:transparent; color:var(--text-secondary); font-weight:700; font-size:0.95rem; cursor:pointer;';
            } else {
                btnM.style.cssText =
                    'padding:10px 28px; border-radius:30px; border:2px solid rgba(99,102,241,0.5); background:var(--accent-grad); color:white; font-weight:700; font-size:0.95rem; cursor:pointer;';
                btnT.style.cssText =
                    'padding:10px 28px; border-radius:30px; border:2px solid var(--border); background:transparent; color:var(--text-secondary); font-weight:700; font-size:0.95rem; cursor:pointer;';
            }
        }

        function openAssign(taskId, taskTitle) {
            document.getElementById('assignTaskId').value = taskId;
            document.getElementById('assignTaskTitle').textContent = taskTitle;
            document.getElementById('assignModal').style.display = 'flex';
        }

        function closeAssign() {
            document.getElementById('assignModal').style.display = 'none';
        }

        document.getElementById('assignModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeAssign();
        });
    </script>
@endpush
