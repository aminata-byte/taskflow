<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // ── CRÉER une tâche dans une colonne ──
    public function store(Request $request, Column $column)
    {
        // Vérifie que la colonne appartient à l'utilisateur connecté
        abort_if($column->project->user_id !== Auth::id(), 403);

        // Validation — priorité en français : basse / moyenne / haute
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:basse,moyenne,haute',
        ]);

        // Créer la tâche associée à la colonne
        $column->tasks()->create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'priority'    => $validated['priority'] ?? 'basse',
        ]);

        return back()->with('success', 'Tâche ajoutée');
    }

    // ── AFFICHER le formulaire de modification ──
    public function edit(Column $column, Task $task)
    {
        $columns = $column->project->columns;

        return view('tasks.edit', compact('task', 'column', 'columns'));
    }

    // ── MODIFIER une tâche ──
    public function update(Request $request, Column $column, Task $task)
    {
        abort_if($column->project->user_id !== Auth::id(), 403);

        // Validation — priorité en français
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:basse,moyenne,haute',
            'column_id'   => 'required|exists:columns,id',
        ]);

        // Mettre à jour (peut changer de colonne)
        $task->update($validated);

        return redirect()
            ->route('projects.show', $column->project_id)
            ->with('success', 'Tâche modifiée ');
    }

    // ── SUPPRIMER une tâche ──
    public function destroy(Column $column, Task $task)
    {
        abort_if($column->project->user_id !== Auth::id(), 403);

        $projectId = $column->project_id;
        $task->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('success', 'Tâche supprimée. 🗑️');
    }
}
