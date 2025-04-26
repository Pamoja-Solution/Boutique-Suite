<div class="">
    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
    @include('gerant.nav')
    @endif
    <div class="py-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Gestion des ventes</h2>
                
                <a href="{{ route('vendeur.stat') }}" type="button" class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2 mb-2">
                    <svg class="w-6 h-6 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
                    </svg>
                    Données
                </a>
            @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                
                <a href="{{ route('stats') }}" type="button" class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2 mb-2">
                    <svg class="w-6 h-6 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
                    </svg>
                    Données Générale
                </a>
                @endif
                <!-- Dashboard metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                        <h3 class="font-semibold text-blue-800 dark:text-blue-200">Ventes récentes</h3>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ count($this->getRecentVentes()) }}</p>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4 border border-yellow-100 dark:border-yellow-800">
                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Produits bientôt expirés</h3>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">{{ count($this->getProduitsExpiration()) }}</p>
                    </div>
                    
                    <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4 border border-red-100 dark:border-red-800">
                        <h3 class="font-semibold text-red-800 dark:text-red-200">Stock faible</h3>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-300">{{ count($this->getProduitsLowStock()) }}</p>
                    </div>
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-lg p-4 border border-gray-700 dark:border-gray-200">
                        @livewire('taux-change-scroller')
                    </div>


                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Liste des produits -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Produits disponibles</h3>
                        
                        <p class="text-gray-600 dark:text-gray-400">Recherchez des produits, ajoutez-les au panier et finalisez les ventes</p>
                        
                        <div class="mt-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input 
                                    wire:model.live="search" 
                                    type="text" 
                                    class="pl-10 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                                    placeholder="Rechercher un produit...">
                            </div>
                        </div>
                        <div class="overflow-x-auto mt-4">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prix</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Référence</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($produits as $produit)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $produit->nom }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($produit->prix_vente, 2) }} Fc</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $produit->stock }} {{ $produit->unite_mesure }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $produit->reference_interne }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button 
                                                    wire:click="openModal('details', {{ $produit->id }})"
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2">
                                                    Détails
                                                </button>
                                                <button 
                                                    wire:click="addToCart({{ $produit->id }})"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                    + Panier
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                Aucun produit trouvé
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $produits->links() }}
                        </div>
                    </div>
                </div>
                
               <!-- Section du panier améliorée -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Panier</h3>
                        
                        @if(count($this->selectedProduits) > 0)
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-1">
                                    
                                    <button 
                                        wire:click="openNewClientModal" 
                                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                    >
                                        + Nouveau client
                                    </button>
                                </div>
                                
                                <!-- Sélection de client avec recherche -->
                                <div class="relative">
                                    <div class="flex">
                                        <div class="relative flex-grow">
                                            <input 
                                                type="text" 
                                                id="clientSearch" 
                                                wire:model.live="clientSearch"
                                                wire:keydown.enter.prevent="searchClients"
                                                placeholder="Rechercher un client..."
                                                class="w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pr-10 dark:bg-gray-700 dark:text-white"
                                            >
                                            @if($clientId)
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Résultats de recherche -->
                                    @if($clientSearch && count($filteredClients) > 0 && !$clientId)
                                        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 shadow-lg rounded-md border border-gray-200 dark:border-gray-600 max-h-60 overflow-y-auto">
                                            <ul>
                                                @foreach($filteredClients as $client)
                                                    <li>
                                                        <button
                                                            wire:click="selectClient({{ $client->id }})"
                                                            class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 text-sm"
                                                        >
                                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $client->nom }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $client->telephone }}</div>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @elseif($clientSearch && count($filteredClients) == null)
                                        <div class="bg-red-400 dark:bg-red-600 px-6 rounded rounded-lg">
                                            <p class="my-4 text-gray-900 dark:text-white px-6" >Aucun client trouvé</p>
                                        </div>
                                    @endif
                                    
                                    <!-- Client sélectionné -->
                                    @if($clientId)
                                        @php $selectedClient = App\Models\Client::find($clientId); @endphp
                                        @if($selectedClient)
                                            <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900 rounded-md border border-blue-100 dark:border-blue-800">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $selectedClient->nom }} </p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedClient->telephone }}</p>
                                                        @if($selectedClient->email)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedClient->email }}</p>
                                                        @endif
                                                    </div>
                                                    <button 
                                                        wire:click="$set('clientId', null)" 
                                                        class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @error('clientId') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <!-- Liste des produits dans le panier -->
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Articles ({{ count($this->selectedProduits) }})</h4>
                                <div class="space-y-3">
                                    @foreach($panier as $prod)
                                        <div class="flex flex-col p-3 border border-gray-200 dark:border-gray-700 rounded-md">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $prod->nom }}</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($prod->prix_vente, 2) }} Fc/{{ $prod->unite_mesure }}</p>
                                                </div>
                                                <button 
                                                    wire:click="removeFromCart({{ $prod->id }})"
                                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-1">
                                                <div class="flex items-center">
                                                    <span class="text-gray-500 dark:text-gray-400 text-sm mr-2">Quantité:</span>
                                                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-md">
                                                        <button 
                                                            wire:click="decrementQuantity({{ $prod->id }})"
                                                            type="button"
                                                            class="px-2 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-l-md focus:outline-none"
                                                        >
                                                            <!-- Icône moins -->
                                                        </button>
                                                        <input 
                                                            type="text" 
                                                            inputmode="numeric"
                                                            wire:model.lazy="quantities.{{ $prod->id }}"
                                                            wire:change="updateQuantity({{ $prod->id }}, $event.target.value)"
                                                            class="w-12 border-0 text-center focus:ring-0 dark:bg-gray-700 dark:text-white"
                                                        >
                                                        <button 
                                                            wire:click="incrementQuantity({{ $prod->id }})"
                                                            type="button"
                                                            class="px-2 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-r-md focus:outline-none"
                                                        >
                                                            <!-- Icône plus -->
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($prod->prix_vente * ($quantities[$prod->id] ?? 1), 2) }} Fc
                                                </div>
                                            </div>
                                            
                                            @error('quantities.'.$prod->id) 
                                                <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                            
                                            @if(isset($quantities[$prod->id]) && $quantities[$prod->id] > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Stock disponible: {{ $prod->stock }} {{ $prod->unite_mesure }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between font-medium text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <span>Sous-total:</span>
                                    <span>{{ number_format($total, 2) }} Fc</span>
                                </div>
                                
                                <div class="flex justify-between font-semibold text-lg text-gray-900 dark:text-gray-100">
                                    <span>Total:</span>
                                    <span>{{ number_format($total, 2) }} Fc</span>
                                </div>
                                
                                <button 
                                    wire:click="confirmSale"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmSale"
                                    type="button"
                                    class="mt-4 w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition-colors duration-200"
                                >
                                    <!-- Texte normal (visible quand pas en chargement) -->
                                    <span wire:loading.class="hidden" wire:target="confirmSale">
                                        Finaliser la vente
                                    </span>
                                    
                                    <!-- État de chargement (visible pendant le traitement) -->
                                    <span wire:loading.class.remove="hidden" wire:target="confirmSale" class="hidden items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Traitement...
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2">Votre panier est vide</p>
                                <p class="text-sm mt-1">Ajoutez des produits depuis la liste pour commencer.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        @if($showModal)
            <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        @if($modalType === 'details' && $selectedProduit)
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                            {{ $selectedProduit->nom }}
                                        </h3>
                                        <div class="mt-4 space-y-3">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix de vente</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($selectedProduit->prix_vente, 2) }} Fc</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix d'achat</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($selectedProduit->prix_achat, 2) }} Fc</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $selectedProduit->stock }} {{ $selectedProduit->unite_mesure }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Référence</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $selectedProduit->reference_interne }}</p>
                                                </div>
                                                @if($selectedProduit->date_expiration)
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date d'expiration</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $selectedProduit->date_expiration->format('d/m/Y') }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Fournisseur</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $selectedProduit->fournisseur->nom ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sous-rayon</h4>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $selectedProduit->sousRayon->nom ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            @if($modalType === 'details' && $selectedProduit)
                                <button 
                                    wire:click="addToCart({{ $selectedProduit->id }})"
                                    type="button" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 dark:bg-indigo-700 text-base font-medium text-white hover:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm">
                                    Ajouter au panier
                                </button>
                            @endif
                            
                            <button 
                                wire:click="closeModal"
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
                <!-- Modal pour créer un nouveau client -->
        @if($showModal && $modalType === 'new-client')
            <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        Nouveau client
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                                                <input 
                                                    type="text" 
                                                    id="nom" 
                                                    wire:model.defer="newClient.nom"
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                                >
                                                @error('newClient.nom') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                        </div>
                                        
                                        <div>
                                            <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                                            <input 
                                                type="text" 
                                                id="telephone" 
                                                wire:model.defer="newClient.telephone"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                            >
                                            @error('newClient.telephone') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email (optionnel)</label>
                                            <input 
                                                type="email" 
                                                id="email" 
                                                wire:model.defer="newClient.email"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                            >
                                            @error('newClient.email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="adresse" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse (optionnel)</label>
                                            <textarea 
                                                id="adresse" 
                                                wire:model.defer="newClient.adresse" 
                                                rows="2" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                            ></textarea>
                                            @error('newClient.adresse') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button 
                                    wire:click="createClient"
                                    wire:loading.attr="disabled"
                                    wire:target="createClient"
                                    type="button" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 dark:bg-blue-700 text-base font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors duration-200"
                                >
                                    <span wire:loading.class="hidden" wire:target="createClient">
                                        Créer le client
                                    </span>
                                    <span wire:loading.class.remove="hidden" wire:target="createClient" class="hidden flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Traitement...
                                    </span>
                                </button>
                                
                                <button 
                                    wire:click="closeModal"
                                    wire:loading.attr="disabled"
                                    type="button" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                                >
                                    Annuler
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    
        <!-- Affichage des erreurs de validation -->
        @if($hasValidationErrors)
            <div class="fixed bottom-4 left-4 max-w-sm p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-800 rounded-md shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 dark:text-red-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Veuillez corriger les erreurs suivantes:
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @if(count($this->selectedProduits) == 0)
                                    <li>Veuillez sélectionner au moins un produit</li>
                                @endif
                                @if(!$clientId && empty($this->newClient['nom']))
                                    <li>Veuillez sélectionner ou créer un client</li>
                                @endif
                                @foreach ($this->getErrorBag()->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    
            <!-- Notifications -->
            @if(session()->has('message'))
                <div 
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 3000)"
                    x-show="show"
                    class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session()->has('error'))
                <div 
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 3000)"
                    x-show="show"
                    class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg">
                    {{ session('error') }}
                </div>
            @endif
    
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, body }) => {
                        if (status === 419) {
                            // Session expirée
                            alert('Votre session a expiré. Veuillez rafraîchir la page.');
                        } else if (status === 500) {
                            // Erreur serveur
                            alert('Une erreur serveur est survenue. Veuillez réessayer plus tard.');
                        } else if (status === 0) {
                            // Erreur réseau
                            alert('Problème de connexion. Veuillez vérifier votre connexion internet.');
                        }
                    });
                });
            });
        </script>
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('openNewTab', ({ url }) => {
                    window.open(url, '_blank'); // Ouvre dans un nouvel onglet
                });
            });
        </script>
    </div>
</div>