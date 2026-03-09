@extends('layouts.app')

@section('title', 'Nouveau Projet')

@section('content')
    <div class="page-container">

        <div class="page-header" style="margin-bottom:1.5rem; justify-content:center; text-align:center;">
            <div>
                <h1 class="page-title">Nouveau projet</h1>
            </div>
        </div>

        <div class="form-container">
            <div class="form-card">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="name">Nom du projet *</label>
                        <input type="text" id="name" name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"
                            placeholder="Ex: Refonte du site web" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description
                            <span style="color:var(--text-muted); font-weight:400; text-transform:none;">(optionnel)</span>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4"
                            placeholder="Décris les objectifs de ce projet...">{{ old('description') }}</textarea>
                    </div>

                    <div style="display:flex; gap:12px; margin-top:0.5rem;">
                        <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
                            Créer le projet
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
