@extends('layouts.app')

@section('content')
    <div class="page-container">

        <div class="page-header" style="flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-subtitle">Vue en temps réel de toute l'application</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Nouveau membre</a>
                <a href="{{ route('admin.teams.create') }}" class="btn-secondary">+ Nouvelle équipe</a>
            </div>
        </div>

        {{-- Stats globales --}}
        <div class="stats-grid" style="margin-bottom:2rem;">
            <div class="stat-card">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Membres</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalProjects }}</div>
                <div class="stat-label">Projets</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalTeams }}</div>
                <div class="stat-label">Équipes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tasksDone }}</div>
                <div class="stat-label">Tâches terminées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tasksInProgress }}</div>
                <div class="stat-label">En cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tasksTodo }}</div>
                <div class="stat-label">À faire</div>
            </div>
            <div class="stat-card" style="border-color:rgba(239,68,68,0.3);">
                <div class="stat-value" style="color:#EF4444;">{{ $tasksLate }}</div>
                <div class="stat-label">En retard</div>
            </div>
        </div>

        {{-- Tâches en retard --}}
        @if ($lateTasks->isNotEmpty())
            <div class="card" style="margin-bottom:2rem; border:1px solid rgba(239,68,68,0.3); overflow-x:auto;">
                <h2
                    style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; color:#F87171; margin-bottom:1.2rem;">
                    Tâches en retard ({{ $lateTasks->count() }})
                </h2>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:500px;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border);">
                                <th
                                    style="text-align:left; padding:10px 14px; color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase;">
                                    Tâche</th>
                                <th
                                    style="text-align:left; padding:10px 14px; color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase;">
                                    Assigné à</th>
                                <th
                                    style="text-align:left; padding:10px 14px; color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase;">
                                    Projet</th>
                                <th
                                    style="text-align:center; padding:10px 14px; color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase;">
                                    Échéance</th>
                                <th
                                    style="text-align:center; padding:10px 14px; color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase;">
                                    Retard</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lateTasks as $task)
                                @php $daysLate = (int) \Carbon\Carbon::parse($task->due_date)->diffInDays(now()); @endphp
                                <tr style="border-bottom:1px solid var(--border);">
                                    <td style="padding:12px 14px; font-weight:600; font-size:0.88rem;">{{ $task->title }}
                                    </td>
                                    <td style="padding:12px 14px; font-size:0.85rem; color:var(--text-secondary);">
                                        {{ $task->assignedUser?->name ?? '— Non assigné' }}</td>
                                    <td style="padding:12px 14px; font-size:0.85rem;">
                                        @if ($task->column?->project)
                                            <a href="{{ route('projects.show', $task->column->project) }}"
                                                style="color:var(--accent-1); font-weight:600; text-decoration:none;">
                                                {{ $task->column->project->title }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td
                                        style="padding:12px 14px; text-align:center; color:#F87171; font-weight:700; font-size:0.85rem;">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding:12px 14px; text-align:center;">
                                        <span
                                            style="background:rgba(239,68,68,0.15); color:#F87171; padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                            {{ $daysLate }}j
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Grille membres + projets : 1 colonne sur mobile, 2 sur desktop --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">

            {{-- Progression des membres --}}
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700;">Progression des membres
                    </h2>
                    <a href="{{ route('admin.users.index') }}" style="color:var(--accent-1); font-size:0.85rem;">Voir
                        tous</a>
                </div>

                @forelse($members as $member)
                    @php
                        $total = $member->assigned_tasks_count;
                        $pct = $total > 0 ? round(($member->tasks_done / $total) * 100) : 0;
                    @endphp
                    <div style="margin-bottom:1.2rem; padding-bottom:1.2rem; border-bottom:1px solid var(--border);">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div
                                    style="width:34px; height:34px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; color:white; flex-shrink:0;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem;">{{ $member->name }}</div>
                                    <div style="color:var(--text-muted); font-size:0.75rem; word-break:break-all;">
                                        {{ $member->email }}</div>
                                </div>
                            </div>
                            <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                <span
                                    style="background:rgba(255,255,255,0.06); color:var(--text-secondary); padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;"
                                    title="Terminées">{{ $member->tasks_done }}</span>
                                <span
                                    style="background:rgba(255,255,255,0.06); color:var(--text-secondary); padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;"
                                    title="En cours">{{ $member->tasks_inprogress }}</span>
                                <span
                                    style="background:rgba(255,255,255,0.06); color:var(--text-secondary); padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;"
                                    title="À faire">{{ $member->tasks_todo }}</span>
                                @if (($member->tasks_late ?? 0) > 0)
                                    <span
                                        style="background:rgba(239,68,68,0.15); color:#F87171; padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;"
                                        title="En retard">{{ $member->tasks_late }}</span>
                                @endif
                            </div>
                        </div>
                        <div style="background:rgba(255,255,255,0.05); border-radius:20px; height:6px; overflow:hidden;">
                            <div
                                style="background:var(--accent-grad); height:100%; width:{{ $pct }}%; border-radius:20px; transition:width 0.5s ease;">
                            </div>
                        </div>
                        <div style="color:var(--text-muted); font-size:0.75rem; margin-top:4px;">{{ $pct }}%
                            complété ({{ $total }} tâches)</div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p class="empty-title">Aucun membre</p>
                        <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Créer un membre</a>
                    </div>
                @endforelse
            </div>

            {{-- Progression des projets --}}
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700;">Progression des projets
                    </h2>
                </div>

                @forelse($projects as $project)
                    <div style="margin-bottom:1.2rem; padding-bottom:1.2rem; border-bottom:1px solid var(--border);">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:6px;">
                            <a href="{{ route('projects.show', $project) }}"
                                style="font-weight:600; font-size:0.9rem; color:var(--text-primary); text-decoration:none;">
                                {{ $project->title }}
                            </a>
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                @if (($project->late_tasks ?? 0) > 0)
                                    <span
                                        style="background:rgba(239,68,68,0.15); color:#F87171; padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                                        {{ $project->late_tasks }} en retard
                                    </span>
                                @endif
                                <span
                                    style="color:var(--accent-1); font-size:0.85rem; font-weight:700;">{{ $project->progress }}%</span>
                            </div>
                        </div>
                        <div style="background:rgba(255,255,255,0.05); border-radius:20px; height:8px; overflow:hidden;">
                            <div
                                style="background:var(--accent-grad); height:100%; width:{{ $project->progress }}%; border-radius:20px; transition:width 0.5s ease;">
                            </div>
                        </div>
                        <div style="color:var(--text-muted); font-size:0.75rem; margin-top:4px;">
                            {{ $project->done_tasks }} / {{ $project->total_tasks }} tâches terminées
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p class="empty-title">Aucun projet</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection
