<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/taskflow.css') }}">
    @stack('styles')
</head>

<body>

    <nav class="navbar-taskflow">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="navbar-logo">
            <span class="logo-icon"></span>
            <span class="logo-text">TaskFlow</span>
        </a>

        {{-- Liens navigation --}}
        <div class="nav-links">

            @auth
                @if (Auth::user()->isAdmin())
                    {{-- Navigation ADMIN --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        Dashboard Admin
                    </a>
                    <a href="{{ route('admin.teams.index') }}"
                        class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                        Équipes
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        Membres
                    </a>
                    <a href="{{ route('projects.index') }}"
                        class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        Projets
                    </a>
                @else
                    {{-- Navigation MEMBRE --}}
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('projects.index') }}"
                        class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        Projets
                    </a>
                @endif
            @endauth

        </div>

        {{-- Utilisateur connecté --}}
        @auth
            <div style="display:flex; align-items:center; gap:12px;">

                {{-- Badge role --}}
                @if (Auth::user()->isAdmin())
                    <span
                        style="background:rgba(245,158,11,0.15); color:#FBBF24; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                        Admin
                    </span>
                @endif

                {{-- Avatar --}}
                <div class="user-avatar" title="{{ Auth::user()->name }}">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary" style="padding:6px 14px; font-size:0.85rem;">
                        Déconnexion
                    </button>
                </form>

            </div>
        @endauth

    </nav>

    <main class="page-container">
        @yield('content')
    </main>

    @stack('scripts')

</body>

</html>
