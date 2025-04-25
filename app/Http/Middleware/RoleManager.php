<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $authUserRole = $user->role;

    // Vérification du statut du compte
    if ($user->status == 0) {
        Auth::logout();
        return redirect()->route('login')->with('error', 'Votre compte est désactivé.');
    }

    // Vérification du rôle
    if ($authUserRole === $role) {
        return $next($request);
    }

    // Redirection basée sur le rôle de l'utilisateur
    return redirect()->to(
        match ($authUserRole) {
            'gerant'      => route('gerant.dashboard'),
            'vendeur'     => route('vendeur.dashboard'),
            'superviseur' => route('superviseur.dashboard'),
            default       => '/'
        }
    )->with('error', 'Accès non autorisé.');
}
}
