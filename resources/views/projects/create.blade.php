{{-- Hérite du layout principal --}}
@extends('layouts.app')

@section('title', 'Nouveau Projet')

@section('content')
    <div class="page-container">

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <a href="{{ route('projects.index') }}">Projets</a>
            <span class="separator">›</span>
            <span>Nouveau projet</span>
        </div>

        {{-- En-tête --}}
        <div class="page-header" style="margin-bottom: 1.5rem;">
            <div>
                <h1 class="page-title"> Nouveau projet</h1>
                <p class="page-subtitle">Les colonnes "À faire", "En cours" et "Terminé" seront créées automatiquement.</p>
            </div>
        </div>

        {{-- ========================================
         FORMULAIRE DE CRÉATION
    ======================================== --}}
        <div class="form-container">
            <div class="form-card">

                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    {{-- Champ : Nom du projet --}}
                    <div class="form-group">
                        <label class="form-label" for="name">Nom du projet *</label>
                        <input type="text" id="name" name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"
                            placeholder="Ex: Refonte du site web" required autofocus>
                        {{-- Afficher l'erreur de validation --}}
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ : Description --}}
                    <div class="form-group">
                        <label class="form-label" for="description">Description <span
                                style="color:var(--text-muted); font-weight:400; text-transform:none;">(optionnel)</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4"
                            placeholder="Décris les objectifs de ce projet...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex; gap:12px; margin-top:0.5rem;">
                        <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
                             Créer le projet
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn-secondary">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>

            {{-- Info box : colonnes auto-créées --}}
            <div class="card"
                style="margin-top:1.25rem; display:flex; gap:1rem; align-items:flex-start; padding:1.25rem;">
                <span style="font-size:1.5rem;"></span>
                <div>
                    <p style="font-weight:600; font-size:0.9rem; margin-bottom:0.25rem; color:var(--text-primary);">Colonnes
                        créées automatiquement</p>
                    <p style="font-size:0.85rem; color:var(--text-secondary);">
                        Ton projet aura automatiquement 3 colonnes :
                        <strong style="color:#818CF8;">À faire</strong>,
                        <strong style="color:#FBBF24;">En cours</strong> et
                        <strong style="color:#34D399;">Terminé</strong>.
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
