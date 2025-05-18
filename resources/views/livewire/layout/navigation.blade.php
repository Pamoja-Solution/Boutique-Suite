<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};

?>

<nav x-data="{ open: false }" class="bg-base-100 border-b border-base-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="navbar max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex-1">
            <!-- Logo -->
            

            <!-- Navigation Links -->
            <div class="hidden sm:flex gap-1 ms-4">
                <a href="{{ route('dashboard') }}"  wire:navigate>
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    {{ __('Dashboard') }}
                </a>
            </div>
        </div>

        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center gap-2">
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost rounded-btn gap-1">
                    <span x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" 
                          x-on:profile-updated.window="name = $event.detail.name"></span>
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </label>
                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 border border-base-200">
                    <li>
                        <a href="{{ route('profile') }}" wire:navigate>
                            <i class="fas fa-user-circle mr-2"></i>
                            {{ __('Profile') }}
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-start">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Log Out') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mobile menu button -->
        <div class="flex-none sm:hidden">
            <button @click="open = !open" class="btn btn-circle btn-ghost">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-transition.opacity class="sm:hidden bg-base-100 shadow-lg">
        <div class="px-2 pt-2 pb-4 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <i class="fas fa-tachometer-alt mr-3"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-base-200 pt-4 pb-4 px-4">
            <div class="flex items-center">
                <div class="avatar mr-3">
                    <div class="w-10 rounded-full">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                    </div>
                </div>
                <div>
                    <div class="font-bold" x-data="{ name: '{{ auth()->user()->name }}' }" 
                         x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="text-sm opacity-70">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    <i class="fas fa-user-edit mr-3"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>