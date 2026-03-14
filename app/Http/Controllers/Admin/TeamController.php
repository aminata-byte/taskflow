<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['members', 'project'])->latest()->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->where('created_by_admin', true)->get();
        $projects = Project::all();
        return view('admin.teams.create', compact('users', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id'  => 'required|exists:projects,id',
            'members'     => 'nullable|array',
            'members.*'   => 'exists:users,id',
        ]);

        $team = Team::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'project_id'  => $validated['project_id'],
            'admin_id'    => auth()->id(),
        ]);

        if (!empty($validated['members'])) {
            $team->members()->attach($validated['members']);
        }

        return redirect()->route('admin.teams.index')
            ->with('success', 'Equipe creee avec succes !');
    }

    public function show(Team $team)
    {
        if ($team->project_id) {
            return redirect()->route('projects.show', $team->project_id)
                ->with('success', 'Équipe : ' . $team->name);
        }

        return redirect()->route('admin.teams.index');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('admin.teams.index')
            ->with('success', 'Equipe supprimee.');
    }

    // Assigner une tache a un membre
    public function assignTask(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
        ]);

        Task::find($validated['task_id'])->update([
            'assigned_to' => $validated['user_id'],
        ]);

        return back()->with('success', 'Tache assignee !');
    }
}
