<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasFactory;

    // ── Champs que l'on peut remplir ──
    protected $fillable = [
        'name',
        'order',
        'project_id',
    ];

    // ── Une colonne appartient à un projet ──
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // ── Une colonne a plusieurs tâches ──
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
