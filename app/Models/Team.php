<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'admin_id', 'project_id'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members');
    }
}
