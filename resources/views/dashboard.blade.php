@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title">Bonjour, {{ Auth::user()->name }}</h1>
                <p class="page-subtitle">Voici un aperçu de tous tes projets et tâches.</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-primary">＋ Nouveau projet</a>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#6366F1;">{{ $totalProjects }}</div>
                    <div class="stat-label">Projets actifs</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#06B6D4;">{{ $totalTasks }}</div>
                    <div class="stat-label">Tâches totales</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#10B981;">{{ $doneTasks }}</div>
                    <div class="stat-label">Terminées</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#F59E0B;">{{ $inProgressTasks }}</div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-value" style="color:#EF4444;">{{ $lateTasks }}</div>
                    <div class="stat-label">En retard</div>
                </div>
            </div>
        </div>

        {{-- Projets récents --}}
        <div style="margin-top:2.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
                <h2 style="font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700;">Projets récents</h2>
                <a href="{{ route('projects.index') }}" class="btn-secondary" style="font-size:0.85rem;">Voir tous →</a>
            </div>

            @if ($recentProjects->isEmpty())
                <div class="empty-state">
                    <p class="empty-title">Aucun projet pour l'instant</p>
                    <p class="empty-text">Crée ton premier projet pour commencer.</p>
                    <a href="{{ route('projects.create') }}" class="btn-primary">＋ Créer mon premier projet</a>
                </div>
            @else
                <div class="projects-grid">
                    @foreach ($recentProjects as $project)
                        @php
                            $total = $project->columns->sum(fn($c) => $c->tasks->count());
                            $done = $project->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count());
                            $progress = $total > 0 ? round(($done / $total) * 100) : 0;
                        @endphp
                        <div class="project-card">
                            <h3 class="project-name">{{ $project->title }}</h3>
                            <p class="project-description">{{ $project->description ?? 'Aucune description.' }}</p>

                            {{-- Barre de progression --}}
                            @if ($total > 0)
                                <div style="margin-bottom:1rem;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                        <span style="color:var(--text-muted); font-size:0.75rem;">Progression</span>
                                        <span
                                            style="color:var(--accent-1); font-size:0.75rem; font-weight:700;">{{ $progress }}%</span>
                                    </div>
                                    <div
                                        style="background:rgba(99,102,241,0.1); border-radius:20px; height:5px; overflow:hidden;">
                                        <div
                                            style="background:var(--accent-grad); height:100%; width:{{ $progress }}%; border-radius:20px;">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="project-meta">
                                <span class="meta-badge">{{ $total }} tâches</span>
                                @if ($done > 0)
                                    <span class="meta-badge"
                                        style="background:rgba(16,185,129,0.08); color:#059669; border-color:rgba(16,185,129,0.2);">
                                        {{ $done }} terminées
                                    </span>
                                @endif
                            </div>

                            <div class="project-actions">
                                <a href="{{ route('projects.show', $project) }}" class="btn-primary"
                                    style="flex:1; justify-content:center;">
                                    Ouvrir →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
