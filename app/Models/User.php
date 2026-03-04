<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ── Champs que l'on peut remplir (mass assignment) ──
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',         // ← on ajoute le rôle
    ];

    // ── Champs cachés (jamais envoyés en JSON) ──
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ── Conversions automatiques des types ──
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Un utilisateur possède plusieurs projets ──
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // ── Vérifier si l'utilisateur est admin ──
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
