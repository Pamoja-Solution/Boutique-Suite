<div>
    @include('gerant.nav')
    <div class="p-4 dark:bg-gray-900 dark:text-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 dark:text-white">Statistiques des Utilisateurs</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Liste des utilisateurs -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 transition-all duration-300 hover:shadow-lg">
                <h2 class="text-lg font-semibold mb-4 dark:text-gray-200">Utilisateurs</h2>
                
                <div class="mb-4">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" 
                        placeholder="Rechercher un utilisateur..." 
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                    >
                </div>
                
                <div class="overflow-y-auto max-h-96 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800">
                    @foreach($users as $user)
                        <div 
                            wire:click="selectUser({{ $user->id }})"
                            class="p-3 mb-2 rounded cursor-pointer transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $selectedUser == $user->id ? 'bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 dark:border-blue-400' : '' }}"
                        >
                            <div class="flex items-center">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mr-3">
                                        <span class="text-gray-600 dark:text-gray-300 font-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium dark:text-gray-200">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->matricule }} - {{ ucfirst($user->role) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($users->isEmpty())
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun utilisateur trouvé
                        </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
            
            <!-- Filtres et statistiques -->
            <div class="md:col-span-2 space-y-6">
                @if($selectedUser)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 transition-all duration-300 hover:shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold dark:text-gray-200">Période d'analyse</h2>
                            <button 
                                wire:click="resetUserSelection"
                                class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded transition duration-200 dark:text-gray-300"
                            >
                                Retour
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                            <button 
                                wire:click="$set('dateRange', 'today')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'today' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Aujourd'hui
                            </button>
                            <button 
                                wire:click="$set('dateRange', 'yesterday')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'yesterday' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Hier
                            </button>
                            <button 
                                wire:click="$set('dateRange', 'week')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'week' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Cette semaine
                            </button>
                            <button 
                                wire:click="$set('dateRange', 'month')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'month' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Ce mois
                            </button>
                            <button 
                                wire:click="$set('dateRange', 'year')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'year' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Cette année
                            </button>
                            <button 
                                wire:click="$set('dateRange', 'custom')" 
                                class="px-3 py-2 rounded-md transition duration-200 {{ $dateRange === 'custom' ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                            >
                                Personnalisé
                            </button>
                        </div>
                        
                        @if($dateRange === 'custom')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de début</label>
                                    <input 
                                        type="date" 
                                        wire:model.live="customStartDate" 
                                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de fin</label>
                                    <input 
                                        type="date" 
                                        wire:model.live="customEndDate" 
                                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 transition-all duration-300 hover:shadow-lg">
                        <h2 class="text-xl font-bold mb-2 dark:text-white">{{ $userStats['user']->name }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Période d'analyse: {{ $userStats['periode']['debut'] }} au {{ $userStats['periode']['fin'] }}
                        </p>
                        
                        <!-- Résumé des performances -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border border-blue-100 dark:border-blue-800/50">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Total des ventes</h3>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $userStats['ventes']['total'] }}</p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">{{ number_format($userStats['ventes']['moyenne_par_jour'], 1) }} ventes/jour</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg border border-green-100 dark:border-green-800/50">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-200 mb-1">Montant total</h3>
                                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($userStats['ventes']['montant'], 0, ',', ' ') }} FC</p>
                                <p class="text-sm text-green-700 dark:text-green-300">{{ number_format($userStats['ventes']['montant_moyen_par_jour'], 0, ',', ' ') }} FC/jour</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg border border-purple-100 dark:border-purple-800/50">
                                <h3 class="text-sm font-medium text-purple-800 dark:text-purple-200 mb-1">Balance financière</h3>
                                <p class="text-2xl font-bold {{ $userStats['finances']['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format($userStats['finances']['balance'], 0, ',', ' ') }} FC
                                </p>
                                <p class="text-sm text-purple-700 dark:text-purple-300">Revenus - Dépenses</p>
                            </div>
                        </div>
                        
                        <!-- Top produits vendus -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3 dark:text-gray-200">Top 5 produits vendus</h3>
                            @if(count($userStats['produits']['top']) > 0)
                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Produit</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Référence</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantité</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($userStats['produits']['top'] as $produit)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ $produit->nom }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ $produit->reference_interne }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ $produit->total_quantity }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ number_format($produit->total_amount, 2, ',', ' ') }} FC</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic p-3 bg-gray-50 dark:bg-gray-700 rounded">Aucun produit vendu sur cette période</p>
                            @endif
                        </div>
                        
                        <!-- Évolution des ventes -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3 dark:text-gray-200">Évolution des ventes</h3>
                            @if(count($userStats['ventes']['evolution']) > 0)
                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre de ventes</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Montant total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($userStats['ventes']['evolution'] as $evolution)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ $evolution->date }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ $evolution->count }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">{{ number_format($evolution->total_amount, 2, ',', ' ') }} FC</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic p-3 bg-gray-50 dark:bg-gray-700 rounded">Aucune vente sur cette période</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center transition-all duration-300 hover:shadow-lg">
                        <div class="text-blue-500 dark:text-blue-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold mb-2 dark:text-white">Sélectionnez un utilisateur</h2>
                        <p class="text-gray-600 dark:text-gray-400">Veuillez sélectionner un utilisateur dans la liste pour afficher ses statistiques détaillées.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>