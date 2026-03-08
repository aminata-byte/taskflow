<x-guest-layout>

    <p
        style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.6; text-align: center;">
        Entrez votre email pour recevoir un code de verification a 6 chiffres.
    </p>

    @if ($errors->any())
        <div
            style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #F87171; border-radius: 10px; padding: 12px 16px; margin-bottom: 1.2rem; font-size: 0.875rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.send') }}">
        @csrf

        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Email
            </label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ton@email.com">
            @error('email')
                <div style="color:#F87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-submit" style="margin-top: 1rem;">
            Envoyer le code OTP
        </button>

        <div style="text-align:center; margin-top:1.2rem;">
            <a href="{{ route('login') }}" style="color:var(--text-secondary); font-size:0.875rem;">
                ← Retour a la connexion
            </a>
        </div>
    </form>

</x-guest-layout>
