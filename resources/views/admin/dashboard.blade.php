@extends('layouts.app')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-subtitle">Vue en temps réel de toute l'application</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Nouveau membre</a>
                <a href="{{ route('admin.teams.create') }}" class="btn-secondary">+ Nouvelle équipe</a>
            </div>
        </div>

        {{-- Stats globales --}}
        <div class="stats-grid" style="margin-bottom:2rem;">
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#6366F1;">{{ $totalUsers }}</div>
                    <div class="stat-label">Membres</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#06B6D4;">{{ $totalProjects }}</div>
                    <div class="stat-label">Projets</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#F59E0B;">{{ $totalTeams }}</div>
                    <div class="stat-label">Équipes</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#10B981;">{{ $tasksDone }}</div>
                    <div class="stat-label">Tâches terminées</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#F59E0B;">{{ $tasksInProgress }}</div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#6366F1;">{{ $tasksTodo }}</div>
                    <div class="stat-label">À faire</div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">

            {{-- Progression des membres --}}
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700;">Progression des membres
                    </h2>
                    <a href="{{ route('admin.users.index') }}" style="color:var(--accent-1); font-size:0.85rem;">Voir tous
                        →</a>
                </div>

                @forelse($members as $member)
                    @php
                        $total = $member->assigned_tasks_count;
                        $pct = $total > 0 ? round(($member->tasks_done / $total) * 100) : 0;
                    @endphp
                    <div style="margin-bottom:1.2rem; padding-bottom:1.2rem; border-bottom:1px solid var(--border);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div
                                    style="width:34px; height:34px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem;">{{ $member->name }}</div>
                                    <div style="color:var(--text-muted); font-size:0.78rem;">{{ $member->email }}</div>
                                </div>
                            </div>
                            <div style="display:flex; gap:6px;">
                                <span
                                    style="background:rgba(16,185,129,0.15); color:#34D399; padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;">{{ $member->tasks_done }}</span>
                                <span
                                    style="background:rgba(245,158,11,0.15); color:#FBBF24; padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;">{{ $member->tasks_inprogress }}</span>
                                <span
                                    style="background:rgba(99,102,241,0.15); color:#818CF8; padding:2px 8px; border-radius:20px; font-size:0.75rem; font-weight:700;">{{ $member->tasks_todo }}</span>
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
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span style="font-weight:600; font-size:0.9rem;">{{ $project->title }}</span>
                            <span
                                style="color:var(--accent-1); font-size:0.85rem; font-weight:700;">{{ $project->progress }}%</span>
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
