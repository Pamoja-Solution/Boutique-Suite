<nav x-data="{ open: false }" class="bg-base-100 rounded-box  border-b border-base-200 shadow-sm mb-6">
    <!-- Desktop Navigation -->
    <div class="navbar max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Logo et liens principaux -->
        <div class="flex-1">
            <!-- Logo (à ajouter) -->
            
            <!-- Liens desktop -->
            <div class="hidden sm:flex gap-1 ml-4">
                <!-- Lien Client -->
                <x-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.index')" wire:navigate >
                    <i class="fas fa-users mr-2"></i>
                    {{ __('Clients') }}
                </x-nav-link>

                <!-- Lien Dépenses -->
                <x-nav-link href="{{ route('depenses') }}" :active="request()->routeIs('depenses')" wire:navigate >
                    <i class="fas fa-receipt mr-2"></i>
                    {{ __('Dépenses') }}
                </x-nav-link>
                <x-nav-link href="{{ route('vente.produits') }}" :active="request()->routeIs('vente.produits')" wire:navigate >
                    <i class="fas fa-cash-register mr-2"></i>
                    {{ __('Ventes') }}
                </x-nav-link>

                @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                <!-- Menu Admin -->
                <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')" wire:navigate >
                    <i class="fas fa-user-cog mr-2"></i>
                    {{ __('Utilisateurs') }}
                </x-nav-link>
                
                <x-nav-link href="{{ route('achats.index') }}" :active="request()->routeIs('achats.index')" wire:navigate >
                    <i class="fas fa-boxes mr-2"></i>
                    {{ __('Stock') }}
                </x-nav-link>
                
                
                
                <x-nav-link href="{{ route('rayons.index') }}" :active="request()->routeIs('rayons.index')" wire:navigate >
                    <i class="fas fa-sitemap mr-2"></i>
                    {{ __('Rayons') }}
                </x-nav-link>

                <!-- Dropdown Produits -->
                <div class="dropdown dropdown-hover dropdown-bottom">
                    <label tabindex="0" class="btn btn-ghost gap-1">
                        <i class="fas fa-box-open"></i>
                        <span>Produits</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </label>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 border border-base-200">
                        <li>
                            <a href="{{ route('produits.index') }}" class="{{ request()->routeIs('produits.index') ? 'active' : '' }}" wire:navigate >
                                <i class="fas fa-box"></i>
                                Produits
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('gestion.codes-barres') }}" class="{{ request()->routeIs('gestion.codes-barres') ? 'active' : '' }}" wire:navigate >
                                <i class="fa-solid fa-barcode"></i>
                                Code-Barre
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('fournisseurs.index') }}" class="{{ request()->routeIs('fournisseurs.index') ? 'active' : '' }}" wire:navigate >
                                <i class="fas fa-truck"></i>
                                Fournisseurs
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Dropdown Paramètres -->
                <div class="dropdown dropdown-hover dropdown-bottom">
                    <label tabindex="0" class="btn btn-ghost gap-1">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </label>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 border border-base-200">
                        <li>
                            <a href="{{route('monnaie.index')}}" wire:navigate >
                                <i class="fas fa-coins"></i>
                                Monnaie
                            </a>
                        </li>
                        <!--li>
                            <a href="{{route('taux')}}" wire:navigate >
                                <i class="fas fa-exchange-alt"></i>
                                Taux
                            </a>
                        </li-->
                        <li>
                            <a href="{{ route('user.statistics') }}" wire:navigate >
                                <i class="fas fa-chart-line"></i>
                                Statistiques
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('inventaires.index') }}" wire:navigate >
                                <i class="fa-solid fa-tags"></i>
                                Inventaires
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rapports') }}" wire:navigate >
                                <i class="fas fa-chart-line"></i>
                                Rapport
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Bouton mobile -->
        <div class="flex-none sm:hidden">
            <button @click="open = !open" class="btn btn-circle btn-ghost text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Menu mobile -->
    <div x-show="open" x-transition.opacity class="sm:hidden">
        <div class="px-2 pt-2 pb-4 space-y-1 bg-base-100 shadow-lg">
            <!-- Liens principaux -->
            <x-responsive-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.index')" wire:navigate >
                <i class="fas fa-users mr-3"></i>
                {{ __('Clients') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('depenses') }}" :active="request()->routeIs('depenses')" wire:navigate >
                <i class="fas fa-receipt mr-3"></i>
                {{ __('Dépenses') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('vente.produits') }}" :active="request()->routeIs('vente.produits')" wire:navigate >
                <i class="fas fa-cash-register mr-3"></i>
                {{ __('Ventes') }}
            </x-responsive-nav-link>
            @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
            <!-- Menu Admin -->
            <x-responsive-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')" wire:navigate >
                <i class="fas fa-user-cog mr-3"></i>
                {{ __('Utilisateurs') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('achats.index') }}" :active="request()->routeIs('achats.index')" wire:navigate >
                <i class="fas fa-boxes mr-3"></i>
                {{ __('Stock') }}
            </x-responsive-nav-link>

            

            <x-responsive-nav-link href="{{ route('rayons.index') }}" :active="request()->routeIs('rayons.index')" wire:navigate >
                <i class="fas fa-sitemap mr-3"></i>
                {{ __('Rayons') }}
            </x-responsive-nav-link>

            <!-- Accordéon Produits -->
            <div class="collapse collapse-arrow bg-base-200">
                <input type="checkbox" class="peer"/> 
                <div class="collapse-title font-medium flex items-center">
                    <i class="fas fa-box-open mr-3"></i>
                    Produits
                </div>
                <div class="collapse-content"> 
                    <x-responsive-nav-link href="{{ route('produits.index') }}" :active="request()->routeIs('produits.index')" wire:navigate >
                        <i class="fas fa-box mr-3"></i>
                        {{ __('Produits') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('gestion.codes-barres') }}" :active="request()->routeIs('gestion.codes-barres')" wire:navigate >
                        <i class="fa-solid fa-barcode"></i>
                            Code-Barre
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link href="{{ route('fournisseurs.index') }}" :active="request()->routeIs('fournisseurs.index')" wire:navigate >
                        <i class="fas fa-truck mr-3"></i>
                        {{ __('Fournisseurs') }}
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Accordéon Paramètres -->
            <div class="collapse collapse-arrow bg-base-200">
                <input type="checkbox" class="peer"/> 
                <div class="collapse-title font-medium flex items-center">
                    <i class="fas fa-cog mr-3"></i>
                    Paramètres
                </div>
                <div class="collapse-content">
                    <x-responsive-nav-link href="{{route('monnaie.index')}}" wire:navigate >
                        <i class="fas fa-coins mr-3"></i>
                        Monnaie
                    </x-responsive-nav-link>
                    <!-- --x-responsive-nav-link href="{{route('taux')}}" wire:navigate >
                        <i class="fas fa-exchange-alt mr-3"></i>
                        Taux
                    < /x-responsive-nav-link-->
                    <x-responsive-nav-link href="{{ route('user.statistics') }}" wire:navigate >
                        <i class="fas fa-chart-line mr-3"></i>
                        Statistiques
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('inventaires.index') }}" wire:navigate >
                            <i class="fa-solid fa-tags"></i>
                            Inventaires
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('rapports') }}" wire:navigate >
                        <i class="fas fa-chart-line mr-3"></i>
                        Rapports
                    </x-responsive-nav-link>
                </div>
            </div>
            @endif

            <!-- Profil utilisateur -->
            <div class="border-t border-base-200 pt-3 mt-3">
                <div class="flex items-center px-3 py-2">
                    @auth
                        @if (auth()->user()->image)
                            <div class="avatar mr-3">
                                <div class="w-10 rounded-full">
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                                </div>
                            </div>
                        @endif
                    @endauth

                    <div>
                        <div class="font-bold">{{ auth()->user()->name }}</div>
                        <div class="text-sm opacity-70">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <x-responsive-nav-link href="{{ route('profile') }}" class="mt-1" wire:navigate >
                    <i class="fas fa-user-edit mr-3"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link wire:navigate >
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>