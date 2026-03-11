@extends('layouts.app')

@section('title', 'Choisir un espace')

@section('content')
    <div style="min-height: 80vh; display:flex; align-items:center; justify-content:center;">
        <div style="max-width: 700px; width:100%; padding: 2rem;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:3rem;">
                <h1
                    style="font-family:'Sora',sans-serif; font-size:2rem; font-weight:800; background:var(--accent-grad); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:0.5rem;">
                    Bienvenue, {{ Auth::user()->name }} !
                </h1>
                <p style="color:var(--text-secondary); font-size:1rem;">
                    Choisissez votre espace de travail
                </p>
            </div>

            {{-- Erreur --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:1.5rem; text-align:center;">
                    {{ $errors->first() }}
                </div>
            @endif

            @php $isAdminCreated = Auth::user()->created_by_admin; @endphp

            {{-- Grille : 1 colonne si créé par admin, 2 colonnes sinon --}}
            <div
                style="display:grid; grid-template-columns:{{ $isAdminCreated ? '1fr' : '1fr 1fr' }}; gap:1.5rem; max-width:{{ $isAdminCreated ? '360px' : '700px' }}; margin:0 auto;">

                {{-- Espace Équipe --}}
                <a href="{{ route('member.team-space') }}"
                    style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1rem;
                      background:var(--bg-card); border:2px solid {{ $hasTeam ? 'rgba(99,102,241,0.4)' : 'var(--border)' }};
                      border-radius:20px; padding:2.5rem 2rem; text-decoration:none;
                      transition:all 0.3s ease; cursor:{{ $hasTeam ? 'pointer' : 'not-allowed' }};"
                    onmouseover="if({{ $hasTeam ? 'true' : 'false' }}) { this.style.borderColor='rgba(99,102,241,0.8)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(99,102,241,0.2)'; }"
                    onmouseout="this.style.borderColor='{{ $hasTeam ? 'rgba(99,102,241,0.4)' : 'var(--border)' }}'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                    {{ !$hasTeam ? 'onclick=event.preventDefault()' : '' }}>

                    <div style="text-align:center;">
                        <div
                            style="font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; color:var(--text-primary); margin-bottom:0.5rem;">
                            Espace Équipe
                        </div>
                        <div style="color:var(--text-secondary); font-size:0.875rem; line-height:1.5;">
                            Voir vos tâches assignées,<br>collaborer avec votre équipe
                        </div>
                    </div>
                    @if ($hasTeam)
                        <span
                            style="background:var(--accent-grad); color:white; padding:4px 14px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                            Disponible
                        </span>
                    @else
                        <span
                            style="background:rgba(239,68,68,0.15); color:#F87171; padding:4px 14px; border-radius:20px; font-size:0.78rem; font-weight:600;">
                            Aucune équipe assignée
                        </span>
                    @endif
                </a>

                {{-- Espace Personnel : masqué si créé par admin --}}
                @if (!$isAdminCreated)
                    <a href="{{ route('dashboard.personal') }}"
                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1rem;
                      background:var(--bg-card); border:2px solid rgba(16,185,129,0.3);
                      border-radius:20px; padding:2.5rem 2rem; text-decoration:none;
                      transition:all 0.3s ease;"
                        onmouseover="this.style.borderColor='rgba(16,185,129,0.7)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(16,185,129,0.15)';"
                        onmouseout="this.style.borderColor='rgba(16,185,129,0.3)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                        <div style="text-align:center;">
                            <div
                                style="font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; color:var(--text-primary); margin-bottom:0.5rem;">
                                Espace Personnel
                            </div>
                            <div style="color:var(--text-secondary); font-size:0.875rem; line-height:1.5;">
                                Gérez vos projets personnels,<br>à votre propre rythme
                            </div>
                        </div>
                        <span
                            style="background:rgba(16,185,129,0.15); color:#34D399; padding:4px 14px; border-radius:20px; font-size:0.78rem; font-weight:700;">
                            Toujours disponible
                        </span>
                    </a>
                @endif

            </div>

        </div>
    </div>
@endsection
