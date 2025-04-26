<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-500 dark:bg-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex ">
                
                    
                    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                        {{ __('Utilisateurs') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('produits.index') }}" :active="request()->routeIs('produits.index')">
                        {{ __('Produits') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('fournisseurs.index') }}" :active="request()->routeIs('fournisseurs.index')">
                        {{ __('Fournisseurs') }}
                    </x-nav-link>
                    
                    @endif
                    
                    <x-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.index')">
                        {{ __('Clients') }}
                    </x-nav-link>
                    
                    
                    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                    <x-nav-link href="{{ route('achats.index') }}" :active="request()->routeIs('achats.index')">
                        {{ __('Achats') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('vendeur.produits') }}" :active="request()->routeIs('vendeur.produits')">
                        {{ __('Ventes') }}
                    </x-nav-link>
                    

                    <x-nav-link href="{{ route('rayons.index') }}" :active="request()->routeIs('rayons.index')">
                        {{ __('Rayons') }}
                    </x-nav-link>
                    <div class="relative group ">
                        <button class="dark:text-white hover:text-accent-light transition-colors duration-200 font-medium flex items-center space-x-1 h-full py-2">  <!-- Ajout de h-full py-2 -->
                            <span class="font-bold">Parametres</span>
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linelinejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute left-0 w-56 mt-2 bg-white rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 z-50 bg-gray-300 dark:bg-gray-700">
                            <div class="py-1 overflow-hidden">
                                <a href="{{route('monnaie.index')}}" class=" block px-4 py-2 w-full  rounded-md mx-1 border-b border-gray-400 transition-colors duration-200 text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-700 dark:border-gray-400 dark:text-white dark:hover:bg-gray-600 me-2 mb-2">
                                    <i class="fas fa-money-bill me-3" ></i>
                                    <span>Monnaie</span>
                                </a>
                                <a href="{{route('taux')}}" class=" block px-4 py-2 w-full  rounded-md mx-1 border-b border-gray-400 transition-colors duration-200 text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-700 dark:border-gray-400 dark:text-white dark:hover:bg-gray-600 me-2 mb-2">
                                    <i class="fas fa-money-bill me-3" ></i>
                                    <span>Taux d'échange</span>
                                </a>
        
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

           

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Tableau de bord') }}
            </x-responsive-nav-link>
            @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
            
            <x-responsive-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                {{ __('Utilisateurs') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link href="{{ route('produits.index') }}" :active="request()->routeIs('produits.index')">
                {{ __('Produits') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link href="{{ route('fournisseurs.index') }}" :active="request()->routeIs('fournisseurs.index')">
                {{ __('Fournisseurs') }}
            </x-responsive-nav-link>
            @endif
            
            <x-responsive-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.index')">
                {{ __('Clients') }}
            </x-responsive-nav-link>
            @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
            
            <x-responsive-nav-link href="{{ route('vendeur.produits') }}" :active="request()->routeIs('vendeur.produits')">
                {{ __('Ventes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('achats.index') }}" :active="request()->routeIs('achats.index')">
                {{ __('Rayons') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('achats.index') }}" :active="request()->routeIs('achats.index')">
                {{ __('Achats') }}
            </x-responsive-nav-link>

            <div class="relative group ">
                <button class="dark:text-white hover:text-accent-light transition-colors duration-200 font-medium flex items-center space-x-1 h-full py-2">  <!-- Ajout de h-full py-2 -->
                    <span class="font-bold">Parametres</span>
                    <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linelinejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="absolute left-0 w-56 mt-2 bg-white rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 z-50 bg-gray-300 dark:bg-gray-700">
                    <div class="py-1 overflow-hidden">
                        <a href="{{route('monnaie.index')}}" class=" block px-4 py-2 w-full  rounded-md mx-1 border-b border-gray-400 transition-colors duration-200 text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-700 dark:border-gray-400 dark:text-white dark:hover:bg-gray-600 me-2 mb-2">
                            <i class="fas fa-money-bill me-3" ></i>
                            <span>Monnaie</span>
                        </a>
                        <a href="{{route('taux')}}" class=" block px-4 py-2 w-full  rounded-md mx-1 border-b border-gray-400 transition-colors duration-200 text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-700 dark:border-gray-400 dark:text-white dark:hover:bg-gray-600 me-2 mb-2">
                            <i class="fas fa-money-bill me-3" ></i>
                            <span>Taux d'échange</span>
                        </a>
                        

                    </div>
                </div>
            </div>

            
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Auth::user()->image)
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                

                
                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </button>

               
            </div>
        </div>
    </div>
</nav>