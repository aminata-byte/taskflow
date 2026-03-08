<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpController extends Controller
{
    // Etape 1 : Afficher formulaire email
    public function showEmailForm()
    {
        return view('auth.otp-email');
    }

    // Etape 2 : Generer et afficher le code OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte trouve avec cet email.',
        ]);

        // Supprimer les anciens OTP
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        // Generer un code a 6 chiffres
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Sauvegarder en base
        DB::table('password_reset_otps')->insert([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Afficher la page avec le code
        return view('auth.otp-verify', [
            'email' => $request->email,
            'otp'   => $otp,
        ]);
    }

    // Etape 3 : Verifier le code OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Code incorrect.'])->withInput();
        }

        if (now()->isAfter($record->expires_at)) {
            DB::table('password_reset_otps')->where('email', $request->email)->delete();
            return back()->withErrors(['otp' => 'Code expire. Recommence.'])->withInput();
        }

        // Code valide -> afficher formulaire nouveau mot de passe
        return view('auth.otp-reset', [
            'email' => $request->email,
            'otp'   => $request->otp,
        ]);
    }

    // Etape 4 : Changer le mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'otp'                   => 'required|string|size:6',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        // Verifier OTP une derniere fois
        $record = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record || now()->isAfter($record->expires_at)) {
            return redirect()->route('otp.email')
                ->withErrors(['email' => 'Session expiree. Recommence.']);
        }

        // Mettre a jour le mot de passe
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Supprimer l'OTP utilise
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Mot de passe modifie avec succes !');
    }
}
