@extends('layouts.app')

@section('title', 'Modifier l\'équipe')

@section('content')
    <div class="page-container">
        <div class="form-container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Modifier l'équipe</h1>
                    <p class="page-subtitle">{{ $team->name }}</p>
                </div>
                <a href="{{ route('admin.teams.index') }}" class="btn-secondary">Retour</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="form-card">
                <form action="{{ route('admin.teams.update', $team) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nom --}}
                    <div class="form-group">
                        <label class="form-label">Nom de l'équipe *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $team->name) }}"
                            required>
                    </div>

                    {{-- Projet (lecture seule) --}}
                    <div class="form-group">
                        <label class="form-label">Projet assigné</label>
                        <input type="text" class="form-control" value="{{ $team->project?->title ?? 'Aucun projet' }}"
                            disabled style="opacity:0.6; cursor:not-allowed;">
                    </div>

                    {{-- Membres --}}
                    <div class="form-group">
                        <label class="form-label">Membres de l'équipe</label>
                        <div
                            style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
                            @forelse($users as $user)
                                <label
                                    style="display:flex; align-items:center; gap:14px; padding:14px 18px; border-bottom:1px solid var(--border); cursor:pointer; transition:background 0.15s;"
                                    onmouseover="this.style.background='rgba(99,102,241,0.06)'"
                                    onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                        {{ $team->members->contains($user->id) ? 'checked' : '' }}
                                        style="width:18px; height:18px; accent-color:var(--accent-1); cursor:pointer; flex-shrink:0;">
                                    <div
                                        style="width:36px; height:36px; background:var(--accent-grad); border-radius:50%;
                                        display:flex; align-items:center; justify-content:center;
                                        font-weight:700; font-size:0.85rem; color:white; flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; font-size:0.9rem;">{{ $user->name }}</div>
                                        <div style="color:var(--text-muted); font-size:0.75rem;">{{ $user->email }}</div>
                                    </div>
                                    @if ($team->members->contains($user->id))
                                        <span
                                            style="margin-left:auto; background:rgba(99,102,241,0.15); color:var(--accent-1); padding:2px 10px; border-radius:20px; font-size:0.72rem; font-weight:700;">
                                            Membre actuel
                                        </span>
                                    @endif
                                </label>
                            @empty
                                <div style="padding:2rem; text-align:center; color:var(--text-muted);">
                                    Aucun membre disponible —
                                    <a href="{{ route('admin.users.create') }}" style="color:var(--accent-1);">créer un
                                        membre</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:1.5rem;">
                        <button type="submit" class="btn-primary"> Enregistrer</button>
                        <a href="{{ route('admin.teams.index') }}" class="btn-secondary">Annuler</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection
