<x-guest-layout>

    {{-- Session Status --}}
    @if (session('status'))
        <div
            style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #34D399; border-radius: 10px; padding: 12px 16px; margin-bottom: 1.2rem; font-size: 0.875rem; text-align: center;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="ton@email.com">
            @error('email')
                <div style="color:#F87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Mot de passe
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••">
            @error('password')
                <div style="color:#F87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember me --}}
        <div style="margin-bottom: 1rem;">
            <label class="auth-card checkbox-label"
                style="display:flex; align-items:center; gap:8px; color:var(--text-secondary); font-size:0.875rem; cursor:pointer;">
                <input type="checkbox" name="remember" style="width:auto; accent-color:var(--accent-1);">
                Se souvenir de moi
            </label>
        </div>

        <button type="submit" class="auth-submit">
            Se connecter
        </button>

        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-top:1.2rem; flex-wrap:wrap; gap:8px;">
            <a href="{{ route('otp.email') }}" style="color:var(--text-secondary); font-size:0.875rem;">
                Mot de passe oublie ?
            </a>
            <a href="{{ route('register') }}" style="color:var(--accent-1); font-size:0.875rem;">
                Creer un compte →
            </a>
        </div>
    </form>

</x-guest-layout>
