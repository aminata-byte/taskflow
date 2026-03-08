<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/personal', [DashboardController::class, 'personal'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.personal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
});

Route::resource('columns.tasks', TaskController::class)
    ->only(['store', 'edit', 'update', 'destroy']);

// ==============================
// RESET MOT DE PASSE PAR OTP
// ==============================
Route::middleware('guest')->group(function () {
    Route::get('/reset-otp',           [App\Http\Controllers\PasswordResetOtpController::class, 'showEmailForm'])->name('otp.email');
    Route::post('/reset-otp/send',     [App\Http\Controllers\PasswordResetOtpController::class, 'sendOtp'])->name('otp.send');
    Route::post('/reset-otp/verify',   [App\Http\Controllers\PasswordResetOtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/reset-otp/password', [App\Http\Controllers\PasswordResetOtpController::class, 'resetPassword'])->name('otp.reset');
});

// ==============================
// ROUTES ADMIN
// ==============================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // routes personnalisées AVANT les resources
    Route::post('users/assign-team', [App\Http\Controllers\Admin\UserController::class, 'assignTeam'])->name('users.assign-team');
    Route::post('users/remove-team', [App\Http\Controllers\Admin\UserController::class, 'removeTeam'])->name('users.remove-team');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::post('teams/assign-task', [App\Http\Controllers\Admin\TeamController::class, 'assignTask'])->name('teams.assign-task');
    Route::resource('teams', App\Http\Controllers\Admin\TeamController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
});

// ==============================
// CHOIX ESPACE DE TRAVAIL
// ==============================
Route::middleware('auth')->group(function () {
    Route::get('/workspace', [App\Http\Controllers\WorkspaceController::class, 'choose'])->name('workspace.choose');
    Route::get('/member/team-space', [App\Http\Controllers\MemberController::class, 'teamSpace'])->name('member.team-space');
    Route::post('/member/move-task', [App\Http\Controllers\MemberController::class, 'moveTask'])->name('member.move-task');
});
