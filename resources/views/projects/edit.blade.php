@extends('layouts.app')
@section('title', 'Modifier — ' . $project->title)

@section('content')
    <div class="page-container">



        <div class="page-header" style="margin-bottom:1.5rem;">
            <div>
                <h1 class="page-title">Modifier le projet</h1>
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
                            <div class="invalid-feedback"> {{ $message }}</div>
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
                            Enregistrer
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>


        </div>

    </div>
@endsection
