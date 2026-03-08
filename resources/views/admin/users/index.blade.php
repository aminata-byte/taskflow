@extends('layouts.app')

@section('content')
    <div class="page-container">

        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des membres</h1>
                <p class="page-subtitle">Créez et gérez les comptes membres</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Nouveau membre</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        <div class="card">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);">
                        <th
                            style="text-align:left; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Membre</th>
                        <th
                            style="text-align:left; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Email</th>
                        <th
                            style="text-align:left; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Équipes</th>
                        <th
                            style="text-align:left; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Projets</th>
                        <th
                            style="text-align:center; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Tâches</th>
                        <th
                            style="text-align:center; padding:12px 16px; color:var(--text-secondary); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;"
                            onmouseover="this.style.background='rgba(99,102,241,0.04)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- Membre --}}
                            <td style="padding:14px 16px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:36px; height:36px; background:var(--accent-grad); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; flex-shrink:0; color:white;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; font-size:0.9rem;">{{ $user->name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td style="padding:14px 16px; color:var(--text-secondary); font-size:0.875rem;">
                                {{ $user->email }}</td>

                            {{-- Équipes (toutes) --}}
                            <td style="padding:14px 16px;">
                                @if ($user->teams->isNotEmpty())
                                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                                        @foreach ($user->teams as $t)
                                            <span
                                                style="background:rgba(99,102,241,0.12); color:#6366F1; border:1px solid rgba(99,102,241,0.25); padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                                                {{ $t->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#EF4444; font-size:0.78rem; font-weight:600;">⚠️ Non assigné</span>
                                @endif
                            </td>

                            {{-- Projets (tous) --}}
                            <td style="padding:14px 16px;">
                                @if ($user->teams->isNotEmpty())
                                    <div style="display:flex; flex-direction:column; gap:4px;">
                                        @foreach ($user->teams as $t)
                                            @if ($t->project)
                                                <a href="{{ route('projects.show', $t->project) }}"
                                                    style="color:var(--accent-1); font-size:0.82rem; font-weight:600; text-decoration:none;">
                                                    📁 {{ $t->project->title }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                                @endif
                            </td>

                            {{-- Tâches --}}
                            <td style="padding:14px 16px; text-align:center;">
                                <span
                                    style="background:rgba(99,102,241,0.12); color:#6366F1; padding:3px 12px; border-radius:20px; font-size:0.82rem; font-weight:700;">
                                    {{ $user->assigned_tasks_count }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="padding:14px 16px; text-align:center;">
                                <div
                                    style="display:flex; gap:8px; justify-content:center; align-items:center; flex-wrap:wrap;">

                                    {{-- Ajouter à une équipe --}}
                                    <button onclick="openAssignTeam({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        class="btn-primary"
                                        style="padding:6px 12px; font-size:0.78rem; white-space:nowrap;">
                                        + Équipe
                                    </button>

                                    {{-- Retirer d'une équipe si membre d'au moins une --}}
                                    @if ($user->teams->isNotEmpty())
                                        <button
                                            onclick="openRemoveTeam({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->teams->toJson() }})"
                                            class="btn-secondary"
                                            style="padding:6px 12px; font-size:0.78rem; white-space:nowrap;">
                                            Retirer
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Supprimer ce membre ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:3rem; color:var(--text-muted);">
                                Aucun membre pour l'instant.
                                <br><br>
                                <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Créer le premier
                                    membre</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL AJOUTER À UNE ÉQUIPE --}}
    <div id="assignTeamModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div
            style="background:var(--bg-card); border-radius:16px; padding:2rem; min-width:380px; border:1px solid var(--border); box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <h3 style="font-family:'Sora',sans-serif; font-weight:700; margin-bottom:0.5rem;">+ Ajouter à une équipe</h3>
            <p id="assignTeamMemberName" style="color:var(--text-secondary); font-size:0.875rem; margin-bottom:1.5rem;"></p>
            <form action="{{ route('admin.users.assign-team') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="assignTeamUserId">
                <div class="form-group">
                    <label class="form-label">Choisir une équipe</label>
                    <select name="team_id" class="form-control" required>
                        <option value="">-- Sélectionner une équipe --</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">
                                {{ $team->name }} — 📁 {{ $team->project?->title ?? 'Sans projet' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:10px; margin-top:1.5rem;">
                    <button type="submit" class="btn-primary">✅ Ajouter</button>
                    <button type="button" onclick="closeAssignTeam()" class="btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL RETIRER D'UNE ÉQUIPE --}}
    <div id="removeTeamModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div
            style="background:var(--bg-card); border-radius:16px; padding:2rem; min-width:380px; border:1px solid var(--border); box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <h3 style="font-family:'Sora',sans-serif; font-weight:700; margin-bottom:0.5rem;">Retirer d'une équipe</h3>
            <p id="removeTeamMemberName" style="color:var(--text-secondary); font-size:0.875rem; margin-bottom:1.5rem;"></p>
            <form action="{{ route('admin.users.remove-team') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="removeTeamUserId">
                <div class="form-group">
                    <label class="form-label">Choisir l'équipe à retirer</label>
                    <select name="team_id" id="removeTeamSelect" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; margin-top:1.5rem;">
                    <button type="submit" class="btn-danger">Retirer</button>
                    <button type="button" onclick="closeRemoveTeam()" class="btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openAssignTeam(userId, userName) {
                document.getElementById('assignTeamUserId').value = userId;
                document.getElementById('assignTeamMemberName').textContent = 'Membre : ' + userName;
                document.getElementById('assignTeamModal').style.display = 'flex';
            }

            function closeAssignTeam() {
                document.getElementById('assignTeamModal').style.display = 'none';
            }

            function openRemoveTeam(userId, userName, teams) {
                document.getElementById('removeTeamUserId').value = userId;
                document.getElementById('removeTeamMemberName').textContent = 'Membre : ' + userName;
                const select = document.getElementById('removeTeamSelect');
                select.innerHTML = '<option value="">-- Sélectionner --</option>';
                teams.forEach(t => {
                    select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
                document.getElementById('removeTeamModal').style.display = 'flex';
            }

            function closeRemoveTeam() {
                document.getElementById('removeTeamModal').style.display = 'none';
            }

            document.getElementById('assignTeamModal').addEventListener('click', function(e) {
                if (e.target === this) closeAssignTeam();
            });
            document.getElementById('removeTeamModal').addEventListener('click', function(e) {
                if (e.target === this) closeRemoveTeam();
            });
        </script>
    @endpush

@endsection
