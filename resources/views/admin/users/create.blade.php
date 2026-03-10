@extends('layouts.app')

@section('content')
    <div class="page-container">
        <div class="form-container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Nouveau membre</h1>
                    <p class="page-subtitle">Créer un compte et assigner à une équipe</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Retour</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="form-card">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            placeholder="Prénom Nom">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required
                            placeholder="membre@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="password" class="form-control" required
                            placeholder="Minimum 8 caractères">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            placeholder="Répéter le mot de passe">
                    </div>

                    {{-- Rôle --}}
                    <div class="form-group">
                        <label class="form-label">Rôle *</label>
                        <select name="role" class="form-control" onchange="toggleTeam(this.value)">
                            <option value="user" {{ old('role') !== 'admin' ? 'selected' : '' }}>Membre</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    {{-- Équipe (masquée si admin) --}}
                    <div id="team-section" style="border-top:1px solid var(--border); margin:1.5rem 0; padding-top:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Assigner à une équipe</label>
                            <select name="team_id" class="form-control">
                                <option value="">Aucune équipe (optionnel)</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}"
                                        {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }} — {{ $team->project?->title ?? 'Sans projet' }}
                                        ({{ $team->members->count() }} membre(s))
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:1.5rem;">
                        <button type="submit" class="btn-primary">Créer</button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function toggleTeam(role) {
            document.getElementById('team-section').style.display = role === 'admin' ? 'none' : 'block';
        }
        // Init
        toggleTeam(document.querySelector('[name="role"]').value);
    </script>
@endsection
