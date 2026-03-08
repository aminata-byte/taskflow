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

        // Tâches par statut (selon nom de colonne — avec accents)
        $tasksDone       = Task::whereHas('column', fn($q) => $q->where('name', 'Terminé'))->count();
        $tasksInProgress = Task::whereHas('column', fn($q) => $q->where('name', 'En cours'))->count();
        $tasksTodo       = Task::whereHas('column', fn($q) => $q->where('name', 'À faire'))->count();

        // Liste de tous les membres avec leurs tâches
        $members = User::where('role', 'user')
            ->withCount('assignedTasks')
            ->with(['assignedTasks.column'])
            ->get()
            ->map(function ($user) {
                $user->tasks_done       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'Terminé')->count();
                $user->tasks_inprogress = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'En cours')->count();
                $user->tasks_todo       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'À faire')->count();
                return $user;
            });

        // Tous les projets avec progression
        $projects = Project::with(['columns.tasks'])->get()->map(function ($project) {
            $allTasks = $project->columns->flatMap->tasks;
            $total    = $allTasks->count();
            $done     = $project->columns
                ->where('name', 'Terminé')
                ->flatMap->tasks
                ->count();
            $progress = $total > 0 ? round(($done / $total) * 100) : 0;
            $project->progress    = $progress;
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
