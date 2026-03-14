@extends('layouts.app')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des équipes</h1>
                <p class="page-subtitle">Créez et gérez les équipes de travail</p>
            </div>
            <a href="{{ route('admin.teams.create') }}" class="btn-primary">+ Nouvelle équipe</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="projects-grid">
            @forelse($teams as $team)
                <div class="project-card">
                    <h3 class="project-name">{{ $team->name }}</h3>
                    <p class="project-description">{{ $team->description ?? 'Aucune description' }}</p>

                    <div class="project-meta">
                        <span class="meta-badge"> {{ $team->project?->title ?? 'Sans projet' }}</span>
                        <span class="meta-badge"> {{ $team->members->count() }} membres</span>
                    </div>

                    {{-- Avatars des membres --}}
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:1rem;">
                        @foreach ($team->members as $member)
                            <div style="width:32px; height:32px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.78rem; border:2px solid var(--bg-card);"
                                title="{{ $member->name }}">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                        @endforeach
                        @if ($team->members->isEmpty())
                            <span style="color:var(--text-muted); font-size:0.82rem;">Aucun membre</span>
                        @endif
                    </div>

                    <div class="project-actions">
                        <a href="{{ route('admin.teams.show', $team) }}" class="btn-primary btn-sm">Voir détails</a>
                        <a href="{{ route('admin.teams.edit', $team) }}" class="btn-secondary"> Modifier</a>
                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                            onsubmit="return confirm('Supprimer cette équipe ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="grid-column:1/-1;">
                    <span class="empty-icon"></span>
                    <h3 class="empty-title">Aucune équipe</h3>
                    <p class="empty-text">Créez votre première équipe</p>
                    <a href="{{ route('admin.teams.create') }}" class="btn-primary">+ Créer une équipe</a>
                </div>
            @endforelse
        </div>

    </div>
@endsection
