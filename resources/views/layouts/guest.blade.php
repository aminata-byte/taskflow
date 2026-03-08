<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TaskFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/taskflow.css') }}">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            animation: fadeInUp 0.4s ease;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo-icon {
            width: 52px;
            height: 52px;
            background: var(--accent-grad);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 12px;
            box-shadow: 0 0 24px rgba(99, 102, 241, 0.4);
        }

        .auth-logo-text {
            font-family: 'Sora', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            background: var(--accent-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-card label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .auth-card input[type="email"],
        .auth-card input[type="password"],
        .auth-card input[type="text"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px;
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .auth-card input:focus {
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .auth-card .form-group {
            margin-bottom: 1.2rem;
        }

        .auth-card .text-red-600 {
            color: #F87171;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .auth-submit {
            width: 100%;
            background: var(--accent-grad);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .auth-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .auth-card a {
            color: var(--accent-1);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .auth-card a:hover {
            color: var(--accent-2);
        }

        .auth-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            flex-wrap: wrap;
            gap: 8px;
        }

        .auth-card input[type="checkbox"] {
            width: auto;
            margin-right: 6px;
            accent-color: var(--accent-1);
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="auth-logo-icon"></div>
                <div class="auth-logo-text">TaskFlow</div>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>

</html>
