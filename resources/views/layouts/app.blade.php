<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    ```
    {{-- Titre de la page --}}
    <title>{{ config('app.name', 'TaskFlow') }} — @yield('title', 'Dashboard')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    {{-- CSS compilé par Vite (app.css + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CSS personnalisé TaskFlow --}}
    <link rel="stylesheet" href="{{ asset('css/taskflow.css') }}">

    {{-- CSS spécifique à certaines pages --}}
    @stack('styles')
    ```

</head>

<body>

    ```
    {{-- ================================
    NAVBAR PRINCIPALE
================================ --}}
    <nav class="navbar-taskflow">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="navbar-logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text">TaskFlow</span>
        </a>

        {{-- Liens navigation --}}
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                🏠 Dashboard
            </a>

            <a href="{{ route('projects.index') }}"
                class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                📋 Projets
            </a>
        </div>

        {{-- Utilisateur connecté --}}
        @auth
            <div style="display:flex; align-items:center; gap:12px;">

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

    {{-- ================================
    CONTENU PRINCIPAL
================================ --}}
    <main class="page-container">
        @yield('content')
    </main>

    {{-- Scripts spécifiques aux pages --}}
    @stack('scripts')
    ```

</body>

</html>
