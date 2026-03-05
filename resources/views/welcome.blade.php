<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskFlow — Gérez vos projets simplement</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/taskflow.css') }}">
    <style>
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 50%);
        }

        .hero-logo {
            width: 72px;
            height: 72px;
            background: var(--accent-grad);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
            animation: fadeInUp 0.5s ease;
        }

        .hero-title {
            font-family: 'Sora', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, #6366F1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: fadeInUp 0.5s ease 0.1s both;
        }

        .hero-subtitle {
            color: var(--text-secondary);
            font-size: 1.15rem;
            max-width: 500px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            animation: fadeInUp 0.5s ease 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.5s ease 0.3s both;
        }

        .hero-features {
            display: flex;
            gap: 2rem;
            margin-top: 4rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeInUp 0.5s ease 0.4s both;
        }

        .feature-item {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-item span {
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="hero">

        <div class="hero-logo">⚡</div>

        <h1 class="hero-title">TaskFlow</h1>

        <p class="hero-subtitle">
            Organisez vos projets, gérez vos tâches et collaborez efficacement avec un tableau Kanban moderne.
        </p>

        <div class="hero-buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">
                    → Mon Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-primary">
                    🚀 Commencer gratuitement
                </a>
                <a href="{{ route('login') }}" class="btn-secondary">
                    Se connecter
                </a>
            @endauth
        </div>

        <div class="hero-features">
            <div class="feature-item"><span>📋</span> Kanban Board</div>
            <div class="feature-item"><span>✅</span> Gestion des tâches</div>
            <div class="feature-item"><span>📊</span> Statistiques</div>
            <div class="feature-item"><span>🔒</span> Sécurisé</div>
        </div>

    </div>
</body>

</html>
