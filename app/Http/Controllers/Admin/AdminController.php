<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;

class AdminController extends Controller
{
    public function index()
    {
        // Stats globales
        $totalUsers    = User::where('role', 'user')->count();
        $totalProjects = Project::count();
        $totalTasks    = Task::count();
        $totalTeams    = Team::count();

        // Taches par statut (selon nom de colonne)
        $tasksDone       = Task::whereHas('column', fn($q) => $q->where('name', 'Termine'))->count();
        $tasksInProgress = Task::whereHas('column', fn($q) => $q->where('name', 'En cours'))->count();
        $tasksTodo       = Task::whereHas('column', fn($q) => $q->where('name', 'A faire'))->count();

        // Liste de tous les membres avec leurs taches
        $members = User::where('role', 'user')
            ->withCount('assignedTasks')
            ->with(['assignedTasks.column'])
            ->get()
            ->map(function ($user) {
                $user->tasks_done       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'Termine')->count();
                $user->tasks_inprogress = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'En cours')->count();
                $user->tasks_todo       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'A faire')->count();
                return $user;
            });

        // Tous les projets avec progression
        $projects = Project::with(['columns.tasks'])->get()->map(function ($project) {
            $allTasks  = $project->columns->flatMap->tasks;
            $total     = $allTasks->count();
            $done      = $allTasks->filter(fn($t) => $t->column?->name === 'Termine')->count();
            $progress  = $total > 0 ? round(($done / $total) * 100) : 0;
            $project->progress   = $progress;
            $project->total_tasks = $total;
            $project->done_tasks  = $done;
            return $project;
        });

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProjects',
            'totalTasks',
            'totalTeams',
            'tasksDone',
            'tasksInProgress',
            'tasksTodo',
            'members',
            'projects'
        ));
    }
}
