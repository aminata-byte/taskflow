<x-guest-layout>

    <p
        style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.6; text-align: center;">
        Code verifie Choisissez votre nouveau mot de passe.
    </p>

    @if ($errors->any())
        <div
            style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #F87171; border-radius: 10px; padding: 12px 16px; margin-bottom: 1.2rem; font-size: 0.875rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.reset') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="otp" value="{{ $otp }}">

        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Nouveau mot de passe
            </label>
            <input type="password" name="password" required placeholder="Minimum 8 caracteres">
            @error('password')
                <div style="color:#F87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Confirmer le mot de passe
            </label>
            <input type="password" name="password_confirmation" required placeholder="Repete le mot de passe">
        </div>

        <button type="submit" class="auth-submit" style="margin-top: 1rem;">
            Changer le mot de passe
        </button>

    </form>

</x-guest-layout>
