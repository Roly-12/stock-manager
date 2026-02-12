<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::all(['id', 'name', 'email', 'role', 'created_at'])
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Modification impossible sur votre propre compte.');
        }

        // Mise à jour de la liste blanche des rôles
        $data = $request->validate([
            'role' => 'required|string|in:admin,gestionnaire,observateur',
        ]);

        $user->update([
            'role' => $data['role']
        ]);

        return back();
    }

    public function destroy(User $user)
    {
        // Sécurité : ne pas se supprimer soi-même
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }
}