<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'column_id',
        'assigned_to'
    ];

    protected $casts = ['due_date' => 'date'];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
}
