<?php
use Illuminate\Support\Facades\Auth;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
new #[Layout('layouts.guest')] class extends Component
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

<div>
    
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <x-validation-errors class="mb-4" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="matricule" :value="__('Matricule')" />
            <x-text-input wire:model="form.matricule" id="matricule" class="block mt-1 w-full mb-2" type="text" name="matricule" required autofocus autocomplete="Matricule" />
            <x-input-error :messages="$errors->get('form.matricule')" class="mt-2" />
                <label for="" class="mt-3 text-gray-900 dark:text-gray-200 text-sm">Le Matricule est donné par l'administrateur</label>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
