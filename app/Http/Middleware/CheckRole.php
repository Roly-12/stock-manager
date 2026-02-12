<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Vérifier si l'utilisateur est connecté
        // 2. Vérifier si son rôle est dans la liste des rôles autorisés
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            return redirect('/dashboard')->with('error', "Accès refusé : vous n'avez pas les permissions nécessaires.");
        }

        return $next($request);
    }
}