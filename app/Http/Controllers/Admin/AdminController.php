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
        $totalUsers    = User::where('role', 'user')->where('created_by_admin', true)->count();
        $totalProjects = Project::count();
        $totalTasks    = Task::count();
        $totalTeams    = Team::count();

        // Tâches par statut
        $tasksDone       = Task::whereHas('column', fn($q) => $q->where('name', 'Terminé'))->count();
        $tasksInProgress = Task::whereHas('column', fn($q) => $q->where('name', 'En cours'))->count();
        $tasksTodo       = Task::whereHas('column', fn($q) => $q->where('name', 'À faire'))->count();

        // Tâches en retard
        $tasksLate = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('column', fn($q) => $q->where('name', '!=', 'Terminé'))
            ->count();

        // Liste détaillée des tâches en retard
        $lateTasks = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('column', fn($q) => $q->where('name', '!=', 'Terminé'))
            ->with(['assignedUser', 'column.project'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Membres avec leurs tâches
        $members = User::where('role', 'user')
            ->where('created_by_admin', true)
            ->withCount('assignedTasks')
            ->with(['assignedTasks.column'])
            ->get()
            ->map(function ($user) {
                $user->tasks_done       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'Terminé')->count();
                $user->tasks_inprogress = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'En cours')->count();
                $user->tasks_todo       = $user->assignedTasks->filter(fn($t) => $t->column?->name === 'À faire')->count();
                $user->tasks_late       = $user->assignedTasks->filter(
                    fn($t) => $t->column?->name !== 'Terminé' && $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast()
                )->count();
                return $user;
            });

        // Tous les projets avec progression
        $projects = Project::with(['columns.tasks'])->get()->map(function ($project) {
            $allTasks = $project->columns->flatMap->tasks;
            $total    = $allTasks->count();
            $done     = $project->columns->where('name', 'Terminé')->flatMap->tasks->count();
            $late     = $project->columns->where('name', '!=', 'Terminé')->flatMap->tasks->filter(
                fn($t) => $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast()
            )->count();

            $project->progress    = $total > 0 ? round(($done / $total) * 100) : 0;
            $project->total_tasks = $total;
            $project->done_tasks  = $done;
            $project->late_tasks  = $late;
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
            'tasksLate',
            'lateTasks',
            'members',
            'projects'
        ));
    }
}
