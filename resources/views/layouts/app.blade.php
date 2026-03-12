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

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 6px;
            background: none;
            border: none;
        }

        .hamburger span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--text-primary, #fff);
            border-radius: 2px;
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-links {
                display: none !important;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: var(--bg-nav, #1a1a2e);
                flex-direction: column !important;
                padding: 1rem;
                gap: 0.5rem !important;
                z-index: 100;
                border-bottom: 1px solid var(--border, rgba(255, 255, 255, 0.1));
            }

            .nav-links.open {
                display: flex !important;
            }

            .nav-links .nav-link {
                padding: 10px 14px !important;
            }

            .navbar-taskflow {
                position: relative;
                flex-wrap: wrap;
            }

            .nav-user-badge {
                display: none !important;
            }

            .btn-logout-text {
                display: none;
            }
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

        {{-- Hamburger (mobile) --}}
        <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        {{-- Liens navigation --}}
        <div class="nav-links" id="nav-menu">
            @auth
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    <a href="{{ route('admin.teams.index') }}"
                        class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">Équipes</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Membres</a>
                    <a href="{{ route('projects.index') }}"
                        class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">Projets</a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    @if (!Auth::user()->created_by_admin)
                        <a href="{{ route('projects.index') }}"
                            class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">Projets</a>
                    @endif
                @endif
            @endauth
        </div>

        {{-- Utilisateur connecté --}}
        @auth
            <div style="display:flex; align-items:center; gap:10px;">
                @if (Auth::user()->isAdmin())
                    <span class="nav-user-badge"
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

    <script>
        function toggleMenu() {
            const menu = document.getElementById('nav-menu');
            menu.classList.toggle('open');
        }
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('nav-menu');
            const hamburger = document.querySelector('.hamburger');
            if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
                menu.classList.remove('open');
            }
        });
    </script>
</body>

</html>
