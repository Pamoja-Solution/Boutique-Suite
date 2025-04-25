<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Livewire\Component;
use Illuminate\Support\Facades\Request;

class Login extends Component
{
    public LoginForm $form;

    public function authenticate()
    {
        try {
            $this->form->authenticate();
            
            // Régénère la session
            session()->regenerate();
            
            // Redirige vers le dashboard
            return redirect()->intended(route('dashboard'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Les erreurs sont automatiquement propagées au formulaire
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}