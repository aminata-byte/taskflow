<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount('assignedTasks')
            ->with('teams.project')
            ->latest()
            ->get();
        $teams = Team::with('project')->get();
        return view('admin.users.index', compact('users', 'teams'));
    }

    public function create()
    {
        $teams = Team::with(['project', 'members'])->get();
        return view('admin.users.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        if (!empty($validated['team_id'])) {
            $team = Team::find($validated['team_id']);
            $team->members()->syncWithoutDetaching([$user->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Membre créé et assigné avec succès !');
    }

    public function assignTeam(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $user->teams()->syncWithoutDetaching([$request->team_id]);

        return back()->with('success', 'Membre ajouté à l\'équipe !');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un admin.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Membre supprimé.');
    }
}
