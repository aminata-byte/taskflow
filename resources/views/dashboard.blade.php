@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="page-container">

        {{-- En-tête --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Bonjour, {{ Auth::user()->name }} 👋</h1>
                <p class="page-subtitle">Voici un aperçu de tous tes projets et tâches.</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-primary">＋ Nouveau projet</a>
        </div>

        {{-- Grille des statistiques --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">📁</div>
                <div>
                    <div class="stat-value" style="color:#818CF8;">{{ $totalProjects }}</div>
                    <div class="stat-label">Projets actifs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">📝</div>
                <div>
                    <div class="stat-value" style="color:#22D3EE;">{{ $totalTasks }}</div>
                    <div class="stat-label">Tâches totales</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div>
                    <div class="stat-value" style="color:#34D399;">{{ $doneTasks }}</div>
                    <div class="stat-label">Terminées</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">⚡</div>
                <div>
                    <div class="stat-value" style="color:#FBBF24;">{{ $inProgressTasks }}</div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">🔥</div>
                <div>
                    <div class="stat-value" style="color:#F87171;">{{ $lateTasks }}</div>
                    <div class="stat-label">En retard</div>
                </div>
            </div>
        </div>

        {{-- Projets récents --}}
        <div style="margin-top:2.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
                <h2 style="font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700;">📋 Projets récents</h2>
                <a href="{{ route('projects.index') }}" class="btn-secondary" style="font-size:0.85rem;">Voir tous →</a>
            </div>

            @if ($recentProjects->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">🗂️</span>
                    <p class="empty-title">Aucun projet pour l'instant</p>
                    <p class="empty-text">Crée ton premier projet pour commencer.</p>
                    <a href="{{ route('projects.create') }}" class="btn-primary">＋ Créer mon premier projet</a>
                </div>
            @else
                <div class="projects-grid">
                    @foreach ($recentProjects as $project)
                        <div class="project-card">

                            {{-- 'title' = vrai nom de la colonne en BDD --}}
                            <h3 class="project-name">{{ $project->title }}</h3>

                            <p class="project-description">{{ $project->description ?? 'Aucune description.' }}</p>

                            <div class="project-meta">
                                <span class="meta-badge">
                                    {{ $project->columns->sum(fn($c) => $c->tasks->count()) }} tâches
                                </span>
                                <span class="meta-badge"
                                    style="background:rgba(16,185,129,0.1); color:#34D399; border-color:rgba(16,185,129,0.2);">
                                    {{ $project->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count()) }}
                                    terminées
                                </span>
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
