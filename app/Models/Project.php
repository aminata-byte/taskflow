<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // ── Champs que l'on peut remplir ──
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    // ── Un projet appartient à un utilisateur ──
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Un projet a plusieurs colonnes (triées par ordre) ──
    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }
}
