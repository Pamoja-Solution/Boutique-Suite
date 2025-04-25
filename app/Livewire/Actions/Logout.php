<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        Auth::guard('web')->logout();

        // Invalider la session et régénérer le token CSRF
        Session::invalidate();
        Session::regenerateToken();
        
        // Nettoyer les données de session si nécessaire
        Session::flush();
    }
}
