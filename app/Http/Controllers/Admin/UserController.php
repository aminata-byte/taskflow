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
        $users = User::where('role', 'user')->where('created_by_admin', true)
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
            'role'     => 'required|in:user,admin',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role'             => $validated['role'],
            'created_by_admin' => true,
        ]);

        // Assigner à une équipe seulement si c'est un membre
        if ($validated['role'] === 'user' && !empty($validated['team_id'])) {
            $team = Team::find($validated['team_id']);
            $team->members()->syncWithoutDetaching([$user->id]);
        }

        $msg = $validated['role'] === 'admin' ? 'Admin créé avec succès !' : 'Membre créé avec succès !';
        return redirect()->route('admin.users.index')->with('success', $msg);
    }

    public function assignTeam(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = User::find($request->user_id);
        $user->teams()->syncWithoutDetaching([$request->team_id]);

        return back()->with('success', 'Membre ajouté à l\'équipe !');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un admin.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Membre supprimé.');
    }
}
