@extends('layouts.app')
@section('title', 'Mes Projets')

@section('content')
    <div class="page-container">

        <div class="page-header" style="flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 class="page-title">Mes Projets</h1>
                <p class="page-subtitle">{{ $projects->count() }} projet(s) au total</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-primary">＋ Nouveau projet</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($projects->isEmpty())
            <div class="empty-state">
                <span class="empty-icon"></span>
                <p class="empty-title">Aucun projet pour l'instant</p>
                <p class="empty-text">Commence par créer ton premier projet.</p>
                <a href="{{ route('projects.create') }}" class="btn-primary">＋ Créer mon premier projet</a>
            </div>
        @else
            <div class="projects-grid">
                @foreach ($projects as $project)
                    @php
                        $team = $project->teams->first();
                        $total = $project->columns->sum(fn($c) => $c->tasks->count());
                        $done = $project->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count());
                        $progress = $total > 0 ? round(($done / $total) * 100) : 0;
                    @endphp
                    <div class="project-card" style="position:relative;">

                        @if ($team)
                            <div style="position:absolute; top:14px; left:14px;">
                                <span
                                    style="background:rgba(99,102,241,0.15); color:#818CF8; border:1px solid rgba(99,102,241,0.3); padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700;">
                                    Équipe : {{ $team->name }}
                                </span>
                            </div>
                        @endif

                        <h3 class="project-name" style="{{ $team ? 'margin-top:2rem;' : '' }}">
                            {{ $project->title }}
                        </h3>

                        <p class="project-description">
                            {{ $project->description ?? 'Aucune description.' }}
                        </p>

                        @if ($total > 0)
                            <div style="margin-bottom:1rem;">
                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <span style="color:var(--text-muted); font-size:0.75rem;">Progression</span>
                                    <span
                                        style="color:var(--accent-1); font-size:0.75rem; font-weight:700;">{{ $progress }}%</span>
                                </div>
                                <div
                                    style="background:rgba(255,255,255,0.05); border-radius:20px; height:5px; overflow:hidden;">
                                    <div
                                        style="background:var(--accent-grad); height:100%; width:{{ $progress }}%; border-radius:20px; transition:width 0.5s;">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="project-meta">
                            <span class="meta-badge">{{ $total }} tâches</span>
                            @if ($done > 0)
                                <span class="meta-badge"
                                    style="background:rgba(16,185,129,0.1); color:#34D399; border-color:rgba(16,185,129,0.2);">
                                    {{ $done }} terminées
                                </span>
                            @endif
                        </div>

                        <div class="project-actions" style="flex-wrap:wrap;">
                            <a href="{{ route('projects.show', $project) }}" class="btn-primary"
                                style="flex:1; justify-content:center; min-width:80px;">Ouvrir</a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">✏️</a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                onsubmit="return confirm('Supprimer ce projet ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">🗑️</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
