@extends('layouts.app')
@section('title', 'Modifier — ' . $project->title)

@section('content')
    <div class="page-container">

        <div class="breadcrumb">
            <a href="{{ route('projects.index') }}">Projets</a>
            <span class="separator">›</span>
            <a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a>
            <span class="separator">›</span>
            <span>Modifier</span>
        </div>

        <div class="page-header" style="margin-bottom:1.5rem;">
            <div>
                <h1 class="page-title">✏️ Modifier le projet</h1>
                <p class="page-subtitle">{{ $project->title }}</p>
            </div>
        </div>

        <div class="form-container">
            <div class="form-card">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="name">Nom du projet *</label>
                        <input type="text" id="name" name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $project->title) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">⚠️ {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">
                            Description
                            <span style="color:var(--text-muted); font-weight:400; text-transform:none;">(optionnel)</span>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
                            💾 Enregistrer
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>

            {{-- Zone danger --}}
            <div class="card"
                style="margin-top:1.25rem; border-color:rgba(239,68,68,0.25); background:rgba(239,68,68,0.05);">
                <h3
                    style="font-family:'Sora',sans-serif; font-size:0.95rem; font-weight:700; color:#F87171; margin-bottom:0.5rem;">
                    ⚠️ Zone de danger
                </h3>
                <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:1rem;">
                    Supprimer ce projet supprimera toutes ses colonnes et tâches.
                </p>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                    onsubmit="return confirm('Supprimer définitivement ce projet ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">🗑️ Supprimer ce projet</button>
                </form>
            </div>
        </div>

    </div>
@endsection
