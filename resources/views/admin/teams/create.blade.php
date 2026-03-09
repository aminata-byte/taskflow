@extends('layouts.app')

@section('content')
    <div class="page-container">
        <div class="form-container">

            <div class="page-header">
                <div>
                    <h1 class="page-title"> Nouvelle équipe</h1>
                    <p class="page-subtitle">Créer une équipe et assigner des membres</p>
                </div>
                <a href="{{ route('admin.teams.index') }}" class="btn-secondary">Retour</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="form-card">
                <form action="{{ route('admin.teams.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Nom de l'équipe *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            placeholder="Ex: Équipe Frontend">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Description de l'équipe...">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Projet assigné *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Choisir un projet</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Membres de l'équipe</label>
                        @if ($users->isEmpty())
                            <div
                                style="color:var(--text-muted); font-size:0.875rem; padding:1rem; background:rgba(255,255,255,0.03); border-radius:10px; border:1px solid var(--border);">
                                Aucun membre disponible.
                                <a href="{{ route('admin.users.create') }}" style="color:var(--accent-1);">Créer un membre
                                    d'abord →</a>
                            </div>
                        @else
                            <div
                                style="display:flex; flex-direction:column; gap:8px; padding:1rem; background:rgba(255,255,255,0.03); border-radius:10px; border:1px solid var(--border);">
                                @foreach ($users as $user)
                                    <label
                                        style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:8px; border-radius:8px; transition:background 0.2s;"
                                        onmouseover="this.style.background='rgba(99,102,241,0.08)'"
                                        onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                            {{ in_array($user->id, old('members', [])) ? 'checked' : '' }}
                                            style="width:auto; accent-color:var(--accent-1);">
                                        <div
                                            style="width:32px; height:32px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; flex-shrink:0;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; font-size:0.9rem;">{{ $user->name }}</div>
                                            <div style="color:var(--text-muted); font-size:0.78rem;">{{ $user->email }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; gap:10px; margin-top:1.5rem;">
                        <button type="submit" class="btn-primary"> Créer l'équipe</button>
                        <a href="{{ route('admin.teams.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
