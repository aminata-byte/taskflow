<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TaskFlow') }} — @yield('title', 'Dashboard')</title>
    <style>
        :root {
            --font-sans: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-body: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/taskflow.css') }}">
    @stack('styles')
</head>

<body>
    <nav class="navbar-taskflow">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="navbar-logo">
            <span class="logo-text">TaskFlow</span>
        </a>

        {{-- Liens navigation --}}
        <div class="nav-links">
            @auth
                @if (Auth::user()->isAdmin())
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
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    @if (!Auth::user()->created_by_admin)
                        <a href="{{ route('projects.index') }}"
                            class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                            Projets
                        </a>
                    @endif
                @endif
            @endauth
        </div>

        {{-- Utilisateur connecté --}}
        @auth
            <div style="display:flex; align-items:center; gap:12px;">
                @if (Auth::user()->isAdmin())
                    <span
                        style="background:rgba(245,158,11,0.15); color:#FBBF24; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                        Admin
                    </span>
                @endif
                <div class="user-avatar" title="{{ Auth::user()->name }}">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
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
