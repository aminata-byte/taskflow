<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // ── Champs que l'on peut remplir ──
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'column_id',
    ];

    // ── Conversion automatique de due_date en objet Carbon (pour les dates) ──
    protected $casts = [
        'due_date' => 'date',
    ];

    // ── Une tâche appartient à une colonne ──
    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    // ── Vérifier si la tâche est en retard ──
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }
}
