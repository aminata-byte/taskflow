<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Column;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Espace équipe : tâches assignées au membre (tous ses projets)
    public function teamSpace()
    {
        $user = auth()->user();

        // Toutes les équipes du membre
        $teams = $user->teams()->with(['project.columns.tasks.assignedUser', 'members'])->get();

        if ($teams->isEmpty()) {
            return redirect()->route('workspace.choose')
                ->withErrors(['error' => "Vous n'êtes membre d'aucune équipe."]);
        }

        // Pour chaque équipe, préparer les données
        $workspaces = $teams->map(function ($team) use ($user) {
            if (!$team->project) return null;

            $columns   = $team->project->columns->keyBy('id');
            $columnIds = $columns->keys();

            // Mes tâches dans CE projet uniquement
            $myTasks = Task::where('assigned_to', $user->id)
                ->whereIn('column_id', $columnIds)
                ->with('column')
                ->get()
                ->groupBy('column_id');

            // Tâches des autres membres de cette équipe (lecture seule)
            $teamTasks = [];
            foreach ($team->members as $member) {
                if ($member->id !== $user->id) {
                    $teamTasks[$member->id] = [
                        'user'  => $member,
                        'tasks' => Task::where('assigned_to', $member->id)
                            ->whereIn('column_id', $columnIds)
                            ->with('column')
                            ->get()
                            ->groupBy('column_id'),
                    ];
                }
            }

            return [
                'team'      => $team,
                'project'   => $team->project,
                'columns'   => $columns,
                'myTasks'   => $myTasks,
                'teamTasks' => $teamTasks,
            ];
        })->filter()->values(); // retire les null (équipes sans projet)

        return view('member.team-space', compact('workspaces', 'user'));
    }

    // Déplacer une tâche (drag & drop)
    public function moveTask(Request $request)
    {
        $request->validate([
            'task_id'   => 'required|exists:tasks,id',
            'column_id' => 'required|exists:columns,id',
        ]);

        $task = Task::findOrFail($request->task_id);

        // Vérifier que la tâche appartient bien à ce membre
        if ($task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $task->update(['column_id' => $request->column_id]);

        return response()->json(['success' => true, 'column' => Column::find($request->column_id)->name]);
    }
}
