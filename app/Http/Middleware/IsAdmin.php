<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Import de la façade Auth pour vérifier l'authentification

class IsAdmin
{
    /**
     *  Vérifie si l'utilisateur est un admin.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔐 On vérifie que l'utilisateur est connecté et est admin
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request); // ✅ autorisé
        }

        // ❌ Rediriger si non autorisé
        return redirect('/')->with('error', 'Accès refusé.');
    }
}
