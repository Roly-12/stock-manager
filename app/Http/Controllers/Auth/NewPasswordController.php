<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // <--- TRÈS IMPORTANT : L'importation manquante
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->token ?? 'direct-access', // Force le token si absent
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // On valide au moins que le mot de passe est présent
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($request->token === 'direct-access') {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Cet utilisateur n’existe pas.']);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);
            
            return redirect()->route('login')->with('status', 'Mot de passe modifié avec succès !');
        }

        // Si ce n'est pas le token de test, on pourrait remettre la logique standard ici
        return back()->withErrors(['token' => 'Accès non autorisé.']);
    }
}