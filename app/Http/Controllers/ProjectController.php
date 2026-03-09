<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Column;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin : seulement les projets créés par l'admin (pas les projets personnels des membres)
            $projects = Project::where('user_id', $user->id)
                ->with(['columns.tasks', 'teams'])->latest()->get();
        } else {
            $projects = Project::where('user_id', $user->id)
                ->with(['columns.tasks', 'teams'])
                ->latest()
                ->get();
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project = Project::create([
            'title'       => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id'     => Auth::id(),
        ]);

        $defaultColumns = ['À faire', 'En cours', 'Terminé'];
        foreach ($defaultColumns as $order => $columnName) {
            Column::create([
                'name'       => $columnName,
                'project_id' => $project->id,
                'order'      => $order + 1,
            ]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès');
    }

    public function show(Project $project)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort_if($project->user_id !== $user->id, 403);
        }

        $project->load([
            'columns.tasks.assignedUser',
            'columns.tasks.column',
            'columns.tasks.notes',
            'teams.members.assignedTasks.column',
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort_if($project->user_id !== $user->id, 403);
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort_if($project->user_id !== $user->id, 403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project->update([
            'title'       => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet modifié avec succès');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort_if($project->user_id !== $user->id, 403);
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet supprimé. 🗑️');
    }
    public function moveTask(Request $request)
    {
        $request->validate([
            'task_id'   => 'required|exists:tasks,id',
            'column_id' => 'required|exists:columns,id',
        ]);

        $task   = \App\Models\Task::findOrFail($request->task_id);
        $column = \App\Models\Column::findOrFail($request->column_id);

        // Vérifier que la colonne appartient à un projet de l'utilisateur
        if ($column->project->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $task->update(['column_id' => $request->column_id]);

        return response()->json(['success' => true]);
    }
}
