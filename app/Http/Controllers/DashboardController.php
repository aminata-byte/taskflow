<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard avec toutes les statistiques de l'utilisateur.
     */
    public function index()
    {
        // Récupère l'utilisateur connecté
        $user = Auth::user();

        // --- Projets de l'utilisateur ---
        $projects = Project::where('user_id', $user->id)
            ->with('columns.tasks')
            ->latest()
            ->get();

        // Nombre total de projets
        $totalProjects = $projects->count();

        // --- Calcul des statistiques des tâches ---
        // On récupère toutes les tâches via les colonnes des projets

        // Total des tâches
        $totalTasks = $projects->sum(fn($p) => $p->columns->sum(fn($c) => $c->tasks->count()));

        // Tâches terminées (dans une colonne nommée "Terminé")
        $doneTasks = $projects->sum(
            fn($p) =>
            $p->columns->where('name', 'Terminé')->sum(fn($c) => $c->tasks->count())
        );

        // Tâches en cours (dans une colonne nommée "En cours")
        $inProgressTasks = $projects->sum(
            fn($p) =>
            $p->columns->where('name', 'En cours')->sum(fn($c) => $c->tasks->count())
        );

        // Tâches en retard (date dépassée ET pas dans "Terminé")
        $lateTasks = $projects->sum(
            fn($p) =>
            $p->columns->where('name', '!=', 'Terminé')->sum(
                fn($c) =>
                $c->tasks->filter(
                    fn($t) =>
                    $t->due_date && \Carbon\Carbon::parse($t->due_date)->isPast()
                )->count()
            )
        );

        // 5 projets les plus récents pour l'affichage
        $recentProjects = $projects->take(5);

        // Envoie toutes les variables à la vue dashboard
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
