<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // ID de l'admin connecté
        $adminId = Auth::id();

        // Stats globales (seulement ce que l'admin a créé)
        $totalUsers = User::where('role', 'user')
            ->where('created_by_admin', true)
            ->count();

        $totalProjects = Project::where('created_by', $adminId)->count();

        $totalTeams = Team::where('created_by', $adminId)->count();

        $totalTasks = Task::whereHas('column.project', function ($q) use ($adminId) {
            $q->where('created_by', $adminId);
        })->count();

        // Tâches par statut (uniquement projets de l'admin)
        $tasksDone = Task::whereHas('column', fn($q) => $q->where('name', 'Terminé'))
            ->whereHas('column.project', fn($q) => $q->where('created_by', $adminId))
            ->count();

        $tasksInProgress = Task::whereHas('column', fn($q) => $q->where('name', 'En cours'))
            ->whereHas('column.project', fn($q) => $q->where('created_by', $adminId))
            ->count();

        $tasksTodo = Task::whereHas('column', fn($q) => $q->where('name', 'À faire'))
            ->whereHas('column.project', fn($q) => $q->where('created_by', $adminId))
            ->count();

        // Tâches en retard
        $tasksLate = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('column', fn($q) => $q->where('name', '!=', 'Terminé'))
            ->whereHas('column.project', fn($q) => $q->where('created_by', $adminId))
            ->count();

        // Liste détaillée des tâches en retard
        $lateTasks = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('column', fn($q) => $q->where('name', '!=', 'Terminé'))
            ->whereHas('column.project', fn($q) => $q->where('created_by', $adminId))
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
                $user->tasks_done = $user->assignedTasks
                    ->filter(fn($t) => $t->column?->name === 'Terminé')
                    ->count();

                $user->tasks_inprogress = $user->assignedTasks
                    ->filter(fn($t) => $t->column?->name === 'En cours')
                    ->count();

                $user->tasks_todo = $user->assignedTasks
                    ->filter(fn($t) => $t->column?->name === 'À faire')
                    ->count();

                $user->tasks_late = $user->assignedTasks
                    ->filter(
                        fn($t) =>
                        $t->column?->name !== 'Terminé' &&
                            $t->due_date &&
                            Carbon::parse($t->due_date)->isPast()
                    )->count();

                return $user;
            });

        // Projets créés par l'admin avec progression
        $projects = Project::where('created_by', $adminId)
            ->with(['columns.tasks'])
            ->get()
            ->map(function ($project) {

                $allTasks = $project->columns->flatMap->tasks;
                $total = $allTasks->count();

                $done = $project->columns
                    ->where('name', 'Terminé')
                    ->flatMap->tasks
                    ->count();

                $late = $project->columns
                    ->where('name', '!=', 'Terminé')
                    ->flatMap->tasks
                    ->filter(
                        fn($t) =>
                        $t->due_date &&
                            Carbon::parse($t->due_date)->isPast()
                    )->count();

                $project->progress = $total > 0 ? round(($done / $total) * 100) : 0;
                $project->total_tasks = $total;
                $project->done_tasks = $done;
                $project->late_tasks = $late;

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
