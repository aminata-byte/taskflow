<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Column;

class ProjectController extends Controller
{
    /**
     * Liste tous les projets de l'utilisateur connecté.
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())
            ->with('columns.tasks')
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Affiche le formulaire de création d'un projet.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Enregistre un nouveau projet + crée les 3 colonnes par défaut.
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Création du projet — on utilise 'title' car c'est le vrai nom de la colonne en BDD
        $project = Project::create([
            'title'       => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id'     => Auth::id(),
        ]);

        // Création automatique des 3 colonnes par défaut
        $defaultColumns = ['À faire', 'En cours', 'Terminé'];
        foreach ($defaultColumns as $order => $columnName) {
            Column::create([
                'name'       => $columnName,
                'project_id' => $project->id,
                'order'      => $order + 1,
            ]);
        }

        // Redirection vers le kanban du projet
        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès');
    }

    /**
     * Affiche le Kanban d'un projet (colonnes + tâches).
     */
    public function show(Project $project)
    {
        // Vérifie que le projet appartient à l'utilisateur connecté
        abort_if($project->user_id !== Auth::id(), 403);

        $project->load('columns.tasks');

        return view('projects.show', compact('project'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Project $project)
    {
        abort_if($project->user_id !== Auth::id(), 403);

        return view('projects.edit', compact('project'));
    }

    /**
     * Met à jour les informations d'un projet.
     */
    public function update(Request $request, Project $project)
    {
        abort_if($project->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Mise à jour avec 'title' (nom réel de la colonne BDD)
        $project->update([
            'title'       => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet modifié avec succès');
    }

    /**
     * Supprime un projet et toutes ses colonnes/tâches (cascade).
     */
    public function destroy(Project $project)
    {
        abort_if($project->user_id !== Auth::id(), 403);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet supprimé. 🗑️');
    }
}
