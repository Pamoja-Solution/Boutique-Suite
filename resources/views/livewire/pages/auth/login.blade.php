<?php
use Illuminate\Support\Facades\Auth;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
new #[Layout('layouts.rien')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Vérification du status de l'utilisateur
        if (Auth::user()->status == 0) {
            Auth::logout();

            // Facultatif : nettoyage session si nécessaire
            Session::invalidate();
            Session::regenerateToken();

            // Redirection avec message d'erreur
            $this->redirect(route('login'), navigate: request()->header('X-Livewire') === 'true');
            session()->flash('error', 'Votre compte est désactivé.');
            return;
        }

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard'), navigate: request()->header('X-Livewire') === 'true');
    }
};
?>
@section('titre','Se Connecter')
<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Image à gauche (visible uniquement sur les grands écrans) -->
    <div class="hidden md:block md:w-1/2 bg-base-200">
        <div class="h-full flex items-center justify-center p-8">
            <img src="{{ asset('blank.jpeg') }}" alt="Illustration de connexion" class=" w-full">
        </div>
    </div>

    <!-- Formulaire à droite -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-md space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-primary">Connexion</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Entrez vos identifiants pour accéder à votre compte</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="alert alert-info mb-4" :status="session('status')" />
            <x-validation-errors class="alert alert-error mb-4" />

            <form wire:submit="login" class="card bg-base-300 shadow-lg">
                <div class="card-body">
                    <!-- Email Address -->
                    <div class="form-control">
                        <label class="label" for="matricule">
                            <span class="label-text">{{ __('Matricule') }}</span>
                        </label>
                        <input wire:model="form.matricule" id="matricule" type="text" 
                               class="input input-bordered w-full" 
                               name="matricule" required autofocus autocomplete="Matricule" />
                        <x-input-error :messages="$errors->get('form.matricule')" class="mt-1 text-error text-sm" />
                        <label class="label">
                            <span class="label-text-alt text-gray-500">Le Matricule est donné par l'administrateur</span>
                        </label>
                    </div>

                    <!-- Password -->
                    <div class="form-control mt-4">
                        <label class="label" for="password">
                            <span class="label-text">{{ __('Password') }}</span>
                        </label>
                        <input wire:model="form.password" id="password" 
                               class="input input-bordered w-full"
                               type="password"
                               name="password"
                               required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-error text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="form-control mt-4">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input wire:model="form.remember" id="remember" type="checkbox" 
                                   class="checkbox checkbox-primary" name="remember">
                            <span class="label-text">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        @if (Route::has('password.request'))
                            <a class="text-sm link link-primary" 
                               href="{{ route('password.request') }}" 
                               wire:navigate>
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            {{ __('Log in') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>