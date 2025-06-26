<div class="">
    
    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
    @include('gerant.nav')
    @endif
    @section('titre','Station de Base')
    <div class="py-6 bg-base-100 min-h-screen rounded-box">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="bg-base-100 shadow-sm rounded-box p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h2 class="text-2xl font-bold text-base-content">Gestion des ventes</h2>
                    @livewire('taux-change-scroller')

                    
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('vendeur.stat') }}" class="btn btn-outline" wire:navigate>
                            <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
                            </svg>
                            Données
                        </a>
                        
                        @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                        <a href="{{ route('stats') }}" class="btn btn-outline" wire:navigate>
                            <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
                            </svg>
                            Données Générale
                        </a>
                        @endif
                    </div>
                </div>
    
                <!-- Dashboard metrics -->
                <!--div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                    <div class="stats bg-primary text-primary-content shadow">
                        <div class="stat">
                            <div class="stat-title">Ventes récentes</div>
                            <div class="stat-value">{ { count($this->getRecentVentes()) }}</div>
                        </div>
                    </div>
                    
                    <div class="stats bg-warning text-warning-content shadow">
                        <div class="stat">
                            <div class="stat-title">Produits bientôt expirés</div>
                            <div class="stat-value">{ { count($this->getProduitsExpiration()) }}</div>
                        </div>
                    </div>
                    
                    <div class="stats bg-error text-error-content shadow">
                        <div class="stat">
                            <div class="stat-title">Stock faible</div>
                            <div class="stat-value">{ { count($this->getProduitsLowStock()) }}</div>
                        </div>
                    </div>
                    
                    <div class="stats bg-base-200 shadow">
                        <div class="stat">
                        </div>
                    </div>
                </div-->
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Liste des produits -->
                <div class="lg:col-span-2">
                    <div class="bg-base-200 shadow-sm rounded-box p-6">
                        <h3 class="text-xl font-bold text-base-content mb-4">Produits disponibles</h3>
                        
                        <p class="text-base-content/70 mb-4">Recherchez des produits, ajoutez-les au panier et finalisez les ventes</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="input input-bordered flex items-center gap-2">
                                    <svg class="w-4 h-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    <input wire:model.live="search" type="text" class="grow" placeholder="Rechercher un produit...">
                                </label>
                            </div>
    
                            <!-- Dans la section panier, près du bouton "Nouveau client" -->
                            <button wire:click="openScanner" class="btn btn-primary">
                                <i class="fa-solid fa-barcode"></i>
                                Scanner
                            </button>
                        </div>

                        <!-- Modal Scanner -->
                        @if($showScannerModal)
                        <div class="modal modal-open">
                            <div class="modal-box max-w-md">
                                <h3 class="font-bold text-lg">Scanner un code-barres</h3>
                                
                                <div class="py-4">
                                    <label class="input input-bordered flex items-center gap-2 mt-4">
                                        <input 
                                            type="text" 
                                            wire:model.live="barcodeInput"
                                            wire:keydown.enter="processBarcodeScan"
                                            id="barcode-scanner-input"
                                            class="grow" 
                                            placeholder="Scannez un code-barres..."
                                            autofocus
                                        />
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </label>
                                    
                                    @error('barcode')
                                        <div class="text-error text-sm mt-2">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Afficher le panier dans le modal -->
                                    @if(count($selectedProduits) > 0)
                                        <div class="mt-6 border-t pt-4">
                                            <h4 class="font-medium mb-2">Panier</h4>
                                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                                @foreach($panier as $prod)
                                                    <div class="flex justify-between items-center">
                                                        <span>{{ $prod->nom }}</span>
                                                        <span class="font-bold">
                                                            {{ $quantities[$prod->id] ?? 1 }} × {{ number_format($prod->prix_vente, 2) }} Fc
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="font-bold text-lg mt-2 border-t pt-2">
                                                Total: {{ number_format($total, 2) }} Fc
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="modal-action">
                                    <button wire:click="closeScanner(false)" class="btn btn-error">
                                        Annuler
                                    </button>
                                    <button wire:click="closeScanner(true)" class="btn btn-primary">
                                        Terminer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('livewire:init', () => {
                            Livewire.on('focusBarcodeInput', () => {
                                const input = document.getElementById('barcode-scanner-input');
                                if (input) {
                                    input.focus();
                                    input.select();
                                }
                            });
                        });
                        </script>
                        @endif
                        
                        <div class="overflow-x-auto mt-4">
                            <table class="table table-zebra">
                                <thead>
                                    <tr >
                                        <th>Nom</th>
                                        <th>Prix</th>
                                        <th>Stock</th>
                                        <th>Référence</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produits as $produit)
                                        <tr wire:key="product-car-{{ $produit->id }}-{{ $loop->index }}" >
                                            <td>
                                                <div >{{ $produit->nom }}</div>
                                            </td>
                                            <td>{{ number_format($produit->prix_vente, 2) }} Fc</td>
                                            <td>{{ $produit->stock }} 
                                                <span class="badge badge-primary">{{ $produit->unite_mesure }}</span>
                                            </td>
                                            <td>{{ $produit->reference_interne }}
                                            </td>
                                            <td>
                                                <div class="flex gap-2">
                                                    <button wire:click="openModal('details', {{ $produit->id }})" class="btn btn-sm btn-ghost text-info">
                                                        Détails
                                                    </button>
                                                    <button wire:click="addToCart({{ $produit->id }})" class="btn btn-sm btn-primary">
                                                        + Panier
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-base-content/70">
                                                Aucun produit trouvé
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                       
                    </div>
                </div>
                
                <!-- Section du panier -->
                <div class="lg:col-span-1">
                    <div class="bg-base-200 shadow-sm rounded-box p-6">
                        <h3 class="text-xl font-bold text-base-content mb-4">Panier</h3>
                        
                        @if(count($this->selectedProduits) > 0)
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <button wire:click="openNewClientModal" class="btn btn-sm btn-ghost text-primary">
                                        + Nouveau client
                                    </button>
                                </div>
                                
                                <!-- Sélection de client -->
                                <div class="form-control">
                                    <label class="input input-bordered flex items-center gap-2">
                                        <input type="text" wire:model.live="clientSearch" placeholder="Rechercher un client...">
                                        @if($clientId)
                                            <span class="text-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </label>
                                    
                                    <!-- Résultats de recherche -->
                                    @if($clientSearch && count($filteredClients) > 0 && !$clientId)
                                        <div class="dropdown dropdown-open w-full">
                                            <ul class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-full border border-base-300 mt-1">
                                                @foreach($filteredClients as $client)
                                                    <li>
                                                        <button wire:click="selectClient({{ $client->id }})" class="text-left">
                                                            <div class="font-medium">{{ $client->nom }}</div>
                                                            <div class="text-xs opacity-70">{{ $client->telephone }}</div>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @elseif($clientSearch && count($filteredClients) == null)
                                        <div class="alert alert-error mt-2">
                                            <span>Aucun client trouvé</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Client sélectionné -->
                                    @if($clientId)
                                        @php $selectedClient = App\Models\Client::find($clientId); @endphp
                                        @if($selectedClient)
                                            <div class="alert alert-info mt-2">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="font-bold">{{ $selectedClient->nom }}</div>
                                                        <div class="text-sm">{{ $selectedClient->telephone }}</div>
                                                        @if($selectedClient->email)
                                                            <div class="text-sm">{{ $selectedClient->email }}</div>
                                                        @endif
                                                    </div>
                                                    <button wire:click="$set('clientId', null)" class="btn btn-sm  btn-error">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @error('clientId') 
                                        <div class="text-error text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Liste des produits dans le panier -->
                            <div class="mt-6 border-t">
                                <h4 class="font-medium text-base-content mb-2 mt-2">Articles ({{ count($this->selectedProduits) }})</h4>
                                <div class="space-y-3">
                                    @foreach($panier as $prod)
                                        <div class="card bg-base-100 shadow-sm" wire:key="panier-card-{{ $prod->id }}-{{ $loop->index }}">
                                            <div class="card-body p-4">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h3 class="card-title">{{ $prod->nom }}</h3>
                                                        <p class="text-sm opacity-70">{{ number_format($prod->prix_vente, 2) }} Fc/{{ $prod->unite_mesure }}</p>
                                                    </div>
                                                    <button wire:click="removeFromCart({{ $prod->id }})" class="btn btn-sm  btn-ghost text-error">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-[1.2em]" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                
                                                <div class="flex justify-between items-center mt-2">
                                                    <div class="join">
                                                        <button wire:click="decrementQuantity({{ $prod->id }})" class="btn btn-sm join-item">
                                                            -
                                                        </button>
                                                        <input type="text" inputmode="numeric" 
                                                               wire:model.lazy="quantities.{{ $prod->id }}"
                                                               wire:change="updateQuantity({{ $prod->id }}, $event.target.value)"
                                                               class="input input-sm input-bordered w-12 text-center join-item">
                                                        <button wire:click="incrementQuantity({{ $prod->id }})" class="btn btn-sm join-item">
                                                            +
                                                        </button>
                                                    </div>
                                                    <div class="font-bold">
                                                        {{ number_format($prod->prix_vente * ($quantities[$prod->id] ?? 1), 2) }} Fc
                                                    </div>
                                                </div>
                                                
                                                @error('quantities.'.$prod->id) 
                                                    <div class="text-error text-xs mt-1">{{ $message }}</div>
                                                @enderror
                                                
                                                @if(isset($quantities[$prod->id]) && $quantities[$prod->id] > 0)
                                                    <div class="text-xs opacity-70 mt-1">
                                                        Stock disponible: {{ $prod->stock }} {{ $prod->unite_mesure }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mt-6 border-t border-base-300 pt-4">
                                <div class="flex justify-between mb-2">
                                    <span>Sous-total:</span>
                                    <span>{{ number_format($total, 2) }} Fc</span>
                                </div>
                                
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span>{{ number_format($total, 2) }} Fc</text-rightspan>
                                </div>
                                
                                <button wire:click="confirmSale" wire:loading.attr="disabled" class="btn btn-primary w-full mt-4">
                                    <span wire:loading.class="hidden" wire:target="confirmSale">Finaliser la vente</span>
                                    <span wire:loading wire:target="confirmSale" class="hidden">
                                        <span class="loading loading-spinner"></span>
                                        Traitement...
                                    </span>
                                </button>

                                @if ($monnaie)
                                    <div class="mt-2 text-sm font-bold text-base-content/70">
                                        Valeur en Dollar: {{ number_format($total / $monnaie->taux_change, 2) }}{{ $monnaie->symbole}}
                                    </div>
                                    
                                @endif
                                    
                                
                            </div>
                            @else
                            <div class="text-center py-8 text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2">Votre panier est vide</p>
                                <p class="text-sm mt-1">Ajoutez des produits depuis la liste pour commencer.</p>
                            </div>
                        
                            <!-- Déplacer ce tableau en dehors du bloc "panier vide" -->
                            <div class="overflow-x-auto mt-8">
                                
                                @if(auth()->user()->role === 'vendeur')
                                <div role="alert" class="alert alert-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-current">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    En cas d'érreur sur une Facture, contactez le gerant pour une modification
                                  </div>                                @endif
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ventesd ?? [] as $key => $recent)
                                            <tr class="bg-base-200">
                                                <td class="text-xs">{{ Str::limit($recent->client->nom, 15) }}</td>
                                                <td>{{ number_format($recent->total, 1, '.', ' ') }}FC</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" 
                                                            onclick="window.open('{{ route('ventes.print-invoice', ['vente' => $recent->id]) }}', '_blank')">
                                                        Imprimer
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Aucune vente récente</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Modal -->
        @if($showModal)
    <!-- Modal pour les détails du produit -->
    <div class="modal modal-open">
        <div class="modal-box max-w-3xl">
            @if($modalType === 'details' && $selectedProduit)
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold">{{ $selectedProduit->nom }}</h3>
                        <div class="mt-4 space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Prix de vente</h4>
                                    <p class="mt-1">{{ number_format($selectedProduit->prix_vente, 2) }} Fc</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Prix d'achat</h4>
                                    <p class="mt-1">{{ number_format($selectedProduit->prix_achat, 2) }} Fc</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Stock</h4>
                                    <p class="mt-1">{{ $selectedProduit->stock }} {{ $selectedProduit->unite_mesure }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Référence</h4>
                                    <p class="mt-1">{{ $selectedProduit->reference_interne }}</p>
                                </div>
                                @if($selectedProduit->date_expiration)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Date d'expiration</h4>
                                    <p class="mt-1">{{ $selectedProduit->date_expiration->format('d/m/Y') }}</p>
                                </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Fournisseur</h4>
                                    <p class="mt-1">{{ $selectedProduit->fournisseur->nom ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Sous-rayon</h4>
                                    <p class="mt-1">{{ $selectedProduit->sousRayon->nom ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="modal-action">
                @if($modalType === 'details' && $selectedProduit)
                    <button 
                        wire:click="addToCart({{ $selectedProduit->id }})"
                        type="button" 
                        class="btn btn-primary"
                    >
                        Ajouter au panier
                    </button>
                @endif
                
                <button 
                    wire:click="closeModal"
                    type="button" 
                    class="btn"
                >
                    Fermer
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Modal pour créer un nouveau client -->
@if($showModal && $modalType === 'new-client')
    <div class="modal modal-open">
        <div class="modal-box max-w-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold">Nouveau client</h3>
                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="nom" 
                                    wire:model.defer="newClient.nom"
                                    class="input input-bordered"
                                >
                                @error('newClient.nom') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Téléphone</span>
                            </label>
                            <input 
                                type="text" 
                                id="telephone" 
                                wire:model.defer="newClient.telephone"
                                class="input input-bordered"
                            >
                            @error('newClient.telephone') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email (optionnel)</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                wire:model.defer="newClient.email"
                                class="input input-bordered"
                            >
                            @error('newClient.email') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Adresse (optionnel)</span>
                            </label>
                            <textarea 
                                id="adresse" 
                                wire:model.defer="newClient.adresse" 
                                rows="2" 
                                class="textarea textarea-bordered"
                            ></textarea>
                            @error('newClient.adresse') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-action">
                <button 
                    wire:click="createClient"
                    wire:loading.attr="disabled"
                    wire:target="createClient"
                    type="button" 
                    class="btn btn-primary"
                >
                    <span wire:loading.class="hidden" wire:target="createClient">
                        Créer le client
                    </span>
                    <span wire:loading.class.remove="hidden" wire:target="createClient" class=" flex items-center">
                        <span class="loading loading-spinner"></span>
                        Traitement...
                    </span>
                </button>
                
                <button 
                    wire:click="closeModal"
                    wire:loading.attr="disabled"
                    type="button" 
                    class="btn"
                >
                    Annuler
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Affichage des erreurs de validation -->
@if($hasValidationErrors)
    <div class="toast toast-bottom toast-start">
        <div class="alert alert-error">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-bold">Veuillez corriger les erreurs suivantes:</h3>
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
        class="toast toast-bottom toast-end">
        <div class="alert alert-success">
            <span>{{ session('message') }}</span>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div 
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        class="toast toast-bottom toast-end">
        <div class="alert alert-error">
            <span>{{ session('error') }}</span>
        </div>
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

    window.addEventListener('sale-confirmed', event => {
    window.open(event.detail.url, '_blank');
});
</script>