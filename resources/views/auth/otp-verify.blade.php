<x-guest-layout>

    <p
        style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.6; text-align: center;">
        Un code a ete genere pour <strong style="color: var(--accent-1);">{{ $email }}</strong>
    </p>

    {{-- Affichage du code OTP --}}
    <div
        style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3); border-radius: 14px; padding: 1.5rem; text-align: center; margin-bottom: 1.5rem;">
        <p
            style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Votre code OTP
        </p>
        <div
            style="font-size: 2.5rem; font-weight: 800; letter-spacing: 0.3em; color: var(--accent-1); font-family: 'Courier New', monospace;">
            {{ $otp }}
        </div>
        <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.5rem;">
            ⏱ Valide pendant 10 minutes
        </p>
    </div>

    @if ($errors->any())
        <div
            style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #F87171; border-radius: 10px; padding: 12px 16px; margin-bottom: 1.2rem; font-size: 0.875rem;">
            {{ $errors->first('otp') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <label
                style="display:block; font-size:0.82rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                Entrez le code a 6 chiffres
            </label>
            <input type="text" name="otp" maxlength="6" required autofocus placeholder="000000"
                style="text-align: center; font-size: 1.5rem; font-weight: 700; letter-spacing: 0.3em;">
            @error('otp')
                <div style="color:#F87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-submit" style="margin-top: 1rem;">
            Verifier le code →
        </button>

        <div style="text-align:center; margin-top:1.2rem;">
            <a href="{{ route('otp.email') }}" style="color:var(--text-secondary); font-size:0.875rem;">
                ← Recommencer
            </a>
        </div>
    </form>

</x-guest-layout>
