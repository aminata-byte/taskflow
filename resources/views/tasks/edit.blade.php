@extends('layouts.app')
@section('title', 'Modifier la tâche')

@section('content')
    <div class="page-container">

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <a href="{{ route('projects.index') }}">Projets</a>
            <span class="separator">›</span>
            {{-- 'title' = vrai nom de la colonne en BDD --}}
            <a href="{{ route('projects.show', $column->project) }}">{{ $column->project->title }}</a>
            <span class="separator">›</span>
            <span>Modifier la tâche</span>
        </div>

        {{-- En-tête --}}
        <div class="page-header" style="margin-bottom:1.5rem;">
            <div>
                <h1 class="page-title">✏️ Modifier la tâche</h1>
                <p class="page-subtitle">Colonne actuelle : <strong>{{ $column->name }}</strong></p>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="form-container">
            <div class="form-card">

                <form action="{{ route('columns.tasks.update', [$column, $task]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Titre de la tâche --}}
                    <div class="form-group">
                        <label class="form-label" for="title">Titre de la tâche *</label>
                        <input type="text" id="title" name="title"
                            class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                            value="{{ old('title', $task->title) }}" required autofocus placeholder="Titre de la tâche...">
                        @error('title')
                            <div class="invalid-feedback"> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="form-label" for="description">
                            Description
                            <span style="color:var(--text-muted); font-weight:400; text-transform:none;">(optionnel)</span>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                            placeholder="Description détaillée...">{{ old('description', $task->description) }}</textarea>
                    </div>

                    {{-- Priorité --}}
                    <div class="form-group">
                        <label class="form-label" for="priority">Priorité</label>
                        <select id="priority" name="priority" class="form-control">
                            <option value="basse" {{ old('priority', $task->priority) === 'basse' ? 'selected' : '' }}>
                                Basse</option>
                            <option value="moyenne" {{ old('priority', $task->priority) === 'moyenne' ? 'selected' : '' }}>
                                Moyenne</option>
                            <option value="haute" {{ old('priority', $task->priority) === 'haute' ? 'selected' : '' }}>
                                Haute</option>
                        </select>
                    </div>

                    {{-- Date d'échéance --}}
                    <div class="form-group">
                        <label class="form-label" for="due_date">
                            Date d'échéance
                            <span style="color:var(--text-muted); font-weight:400; text-transform:none;">(optionnel)</span>
                        </label>
                        <input type="date" id="due_date" name="due_date" class="form-control"
                            value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                    </div>

                    {{-- Déplacer vers une autre colonne --}}
                    <div class="form-group">
                        <label class="form-label" for="column_id">Déplacer vers la colonne</label>
                        <select id="column_id" name="column_id" class="form-control">
                            @foreach ($column->project->columns as $col)
                                <option value="{{ $col->id }}" {{ $col->id === $column->id ? 'selected' : '' }}>
                                    {{ $col->name }} {{ $col->id === $column->id ? '← actuelle' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex; gap:12px; margin-top:0.5rem;">
                        <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
                            💾 Enregistrer
                        </button>
                        <a href="{{ route('projects.show', $column->project) }}" class="btn-secondary">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection
