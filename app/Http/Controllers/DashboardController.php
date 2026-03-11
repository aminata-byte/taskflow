<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin → dashboard admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Membre créé par l'admin → direct espace équipe
        if ($user->created_by_admin) {
            return redirect()->route('member.team-space');
        }

        // Membre inscrit seul → direct espace personnel
        return redirect()->route('dashboard.personal');
    }

    // Dashboard espace personnel
    public function personal()
    {
        $user = Auth::user();

        // Bloquer l'accès espace perso pour les membres créés par admin
        if ($user->created_by_admin) {
            return redirect()->route('member.team-space');
        }

        $projects = Project::where('user_id', $user->id)
            ->with('columns.tasks')
            ->latest()
            ->get();

        $totalProjects   = $projects->count();
        $totalTasks      = $projects->sum(fn($p) => $p->columns->sum(fn($c) => $c->tasks->count()));
        $doneTasks       = $projects->sum(fn($p) => $p->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count()));
        $inProgressTasks = $projects->sum(fn($p) => $p->columns->where('name', 'En cours')->sum(fn($c) => $c->tasks->count()));
        $lateTasks       = $projects->sum(
            fn($p) => $p->columns->where('name', '!=', 'Terminé')->sum(
                fn($c) => $c->tasks->filter(
                    fn($t) => $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast()
                )->count()
            )
        );
        $recentProjects = $projects->take(5);

        return view('dashboard', compact(
            'totalProjects',
            'totalTasks',
            'doneTasks',
            'inProgressTasks',
            'lateTasks',
            'recentProjects'
        ));
    }
}
