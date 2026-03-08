<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    // Page de choix apres connexion
    public function choose()
    {
        $user = auth()->user();

        // Verifier si le membre fait partie d'une equipe
        $hasTeam = $user->teams()->exists();

        return view('workspace.choose', compact('hasTeam'));
    }
}
