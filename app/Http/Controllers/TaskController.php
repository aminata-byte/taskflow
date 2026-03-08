<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private function authorizeColumn(Column $column): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) return;
        abort_if($column->project->user_id !== $user->id, 403);
    }

    public function store(Request $request, Column $column)
    {
        $this->authorizeColumn($column);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:basse,moyenne,haute',
        ]);

        $column->tasks()->create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'priority'    => $validated['priority'] ?? 'basse',
        ]);

        return back()->with('success', 'Tâche ajoutée');
    }

    public function edit(Column $column, Task $task)
    {
        $columns = $column->project->columns;
        return view('tasks.edit', compact('task', 'column', 'columns'));
    }

    public function update(Request $request, Column $column, Task $task)
    {
        $this->authorizeColumn($column);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:basse,moyenne,haute',
            'column_id'   => 'required|exists:columns,id',
        ]);

        $task->update($validated);

        return redirect()
            ->route('projects.show', $column->project_id)
            ->with('success', 'Tâche modifiée');
    }

    public function destroy(Column $column, Task $task)
    {
        $this->authorizeColumn($column);

        $projectId = $column->project_id;
        $task->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('success', 'Tâche supprimée. 🗑️');
    }
}
