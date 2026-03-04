@extends('layouts.app')
@section('title', 'Mes Projets')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title">📋 Mes Projets</h1>
                <p class="page-subtitle">{{ $projects->count() }} projet(s) au total</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-primary">＋ Nouveau projet</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        @if ($projects->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">🗂️</span>
                <p class="empty-title">Aucun projet pour l'instant</p>
                <p class="empty-text">Commence par créer ton premier projet.</p>
                <a href="{{ route('projects.create') }}" class="btn-primary">＋ Créer mon premier projet</a>
            </div>
        @else
            <div class="projects-grid">
                @foreach ($projects as $project)
                    <div class="project-card">

                        {{-- 'title' = vrai nom de la colonne en BDD --}}
                        <h3 class="project-name">{{ $project->title }}</h3>

                        <p class="project-description">
                            {{ $project->description ?? 'Aucune description.' }}
                        </p>

                        <div class="project-meta">
                            <span class="meta-badge">
                                📝 {{ $project->columns->sum(fn($c) => $c->tasks->count()) }} tâches
                            </span>
                            @php
                                $done = $project->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count());
                            @endphp
                            @if ($done > 0)
                                <span class="meta-badge"
                                    style="background:rgba(16,185,129,0.1); color:#34D399; border-color:rgba(16,185,129,0.2);">
                                    ✅ {{ $done }} terminées
                                </span>
                            @endif
                        </div>

                        <div class="project-actions">
                            <a href="{{ route('projects.show', $project) }}" class="btn-primary"
                                style="flex:1; justify-content:center;">Ouvrir</a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">✏️</a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                onsubmit="return confirm('Supprimer ce projet ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">🗑️</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
