<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Column;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Espace equipe : taches assignees au membre
    public function teamSpace()
    {
        $user = auth()->user();

        // Recuperer l'equipe du membre
        $team = $user->teams()->with(['project.columns.tasks.assignedUser', 'members'])->first();

        if (!$team) {
            return redirect()->route('workspace.choose')
                ->withErrors(['error' => "Vous n'êtes membre d'aucune équipe."]);
        }

        // Taches assignees a ce membre, groupees par colonne
        $myTasks = Task::where('assigned_to', $user->id)
            ->with('column')
            ->get()
            ->groupBy('column_id');

        // Colonnes du projet de l'equipe
        $columns = $team->project->columns->keyBy('id');

        // Taches des autres membres de l'equipe (lecture seule)
        $teamTasks = [];
        foreach ($team->members as $member) {
            if ($member->id !== $user->id) {
                $teamTasks[$member->id] = [
                    'user'  => $member,
                    'tasks' => Task::where('assigned_to', $member->id)
                        ->with('column')
                        ->get()
                        ->groupBy('column_id'),
                ];
            }
        }

        return view('member.team-space', compact('team', 'myTasks', 'columns', 'teamTasks'));
    }

    // Deplacer une tache (drag & drop)
    public function moveTask(Request $request)
    {
        $request->validate([
            'task_id'   => 'required|exists:tasks,id',
            'column_id' => 'required|exists:columns,id',
        ]);

        $task = Task::findOrFail($request->task_id);

        // Verifier que la tache appartient bien a ce membre
        if ($task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $task->update(['column_id' => $request->column_id]);

        return response()->json(['success' => true, 'column' => Column::find($request->column_id)->name]);
    }
}
