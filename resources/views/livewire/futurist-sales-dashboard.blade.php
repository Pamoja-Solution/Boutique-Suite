<div class="">
    @include('gerant.nav')

    <!-- En-tête avec titre et boutons d'action -->
    <div class="p-6 bg-base-100 rounded-box mt-6 flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord des Ventes</h1>
        
        <div class="flex flex-wrap gap-2">
            <!-- Bouton pour afficher/cacher les filtres -->
            <button class="btn btn-outline" wire:click="$toggle('showFilters')">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                {{ $showFilters ? 'Masquer les filtres' : 'Afficher les filtres' }}
            </button>
            
            <!-- Bouton d'export -->
            <button class="btn btn-primary" wire:click="$set('showExportModal', true)">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Exporter
            </button>
        </div>
    </div>
    
    <!-- Filtres -->
    @if($showFilters)
        <div class="bg-base-200 p-4 rounded-box mb-6 transition-all duration-300">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Période -->
                <div>
                    <label class="label">
                        <span class="label-text">Période</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="statsPeriod">
                        <option value="7d">7 derniers jours</option>
                        <option value="30d">30 derniers jours</option>
                        <option value="90d">90 derniers jours</option>
                        <option value="custom">Personnalisé</option>
                    </select>
                </div>
                
                <!-- Dates personnalisées -->
                @if($statsPeriod === 'custom')
                    <div>
                        <label class="label">
                            <span class="label-text">De</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" wire:model.live="startDate">
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">À</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" wire:model.live="endDate">
                    </div>
                @endif
                
                <!-- Client -->
                <div>
                    <label class="label">
                        <span class="label-text">Client</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="selectedClient">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Recherche -->
                <div class="md:col-span-2">
                    <label class="label">
                        <span class="label-text">Recherche</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            class="input input-bordered w-full pl-10" 
                            placeholder="Rechercher par client, référence..."
                            wire:model.live.debounce.500ms="search"
                        >
                        <svg class="w-5 h-5 absolute left-3 top-3 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                </div>
                
                <!-- Réinitialiser -->
                <div class="flex items-end">
                    <button class="btn btn-primary" wire:click="resetFilters">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total des ventes -->
        <div class="stats bg-primary text-primary-content">
            <div class="stat">
                <div class="stat-figure">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="stat-title">Total des ventes</div>
                <div class="stat-value">{{ number_format($this->totalSales, 0, ',', ' ') }} Fc</div>
                <div class="stat-desc flex items-center">
                    @if($this->salesTrend > 0)
                        <svg class="w-4 h-4 mr-1 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.5 4.5 7.5-7.5" />
                        </svg>
                    @elseif($this->salesTrend < 0)
                        <svg class="w-4 h-4 mr-1 text-error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.5-4.5 7.5 7.5" />
                        </svg>
                    @else
                        <svg class="w-4 h-4 mr-1 text-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                        </svg>
                    @endif
                    {{ abs($this->salesTrend) }}% vs période précédente
                </div>
            </div>
        </div>
        
        <!-- Nombre de ventes -->
        <div class="stats bg-secondary text-secondary-content">
            <div class="stat">
                <div class="stat-figure">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </div>
                <div class="stat-title">Nombre de ventes</div>
                <div class="stat-value">{{ $this->salesCount }}</div>
                <div class="stat-desc">{{ number_format($this->averageCart, 0, ',', ' ') }} Fc en moyenne</div>
            </div>
        </div>
        
        <!-- Produits vendus -->
        <div class="stats bg-accent text-accent-content">
            <div class="stat">
                <div class="stat-figure">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <div class="stat-title">Produits vendus</div>
                <div class="stat-value">{{ $this->produitsSold }}</div>
                <div class="stat-desc">Top 5 produits</div>
            </div>
        </div>
        
        <!-- Clients -->
        <div class="stats bg-info text-info-content">
            <div class="stat">
                <div class="stat-figure">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div class="stat-title">Clients actifs</div>
                <div class="stat-value">{{ $clients->count() }}</div>
                <div class="stat-desc">Clients ayant acheté</div>
            </div>
        </div>
    </div>
  
    
    <!-- Top produits -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card bg-base-200 shadow col-span-1">
            <div class="card-body">
                <h2 class="card-title">Top 5 produits</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProduits as $produit)
                                <tr>
                                    <td>{{ $produit->nom }}</td>
                                    <td>{{ $produit->total_quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Aucun produit vendu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Dernières ventes -->
        <div class="card bg-base-200 shadow col-span-2">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="card-title">Dernières ventes</h2>
                    <div class="flex items-center gap-2">
                        <select class="select select-bordered select-sm" wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('id')" class="cursor-pointer">
                                    Réf.
                                    @if($sortField === 'id')
                                        <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDirection === 'asc' ? 'M4.5 15.75l7.5-7.5 7.5 7.5' : 'M19.5 8.25l-7.5 7.5-7.5-7.5' }}" />
                                        </svg>
                                    @endif
                                </th>
                                <th wire:click="sortBy('created_at')" class="cursor-pointer">
                                    Date
                                    @if($sortField === 'created_at')
                                        <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDirection === 'asc' ? 'M4.5 15.75l7.5-7.5 7.5 7.5' : 'M19.5 8.25l-7.5 7.5-7.5-7.5' }}" />
                                        </svg>
                                    @endif
                                </th>
                                <th>Client</th>
                                <th wire:click="sortBy('total')" class="cursor-pointer">
                                    Montant
                                    @if($sortField === 'total')
                                        <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortDirection === 'asc' ? 'M4.5 15.75l7.5-7.5 7.5 7.5' : 'M19.5 8.25l-7.5 7.5-7.5-7.5' }}" />
                                        </svg>
                                    @endif
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>#{{ $sale->id }}</td>
                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->client->nom ?? 'N/A' }}</td>
                                    <td>{{ number_format($sale->total, 0, ',', ' ') }} Fc</td>
                                   
                                    <td>
                                        <div class="flex gap-2">
                                            <button 
                                                class="btn btn-xs btn-ghost"
                                                wire:click="showDetails({{ $sale->id }})"
                                                title="Détails"
                                            >
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </button>
                                            <button 
                                                class="btn btn-xs btn-ghost"
                                                
                                                wire:click="printInvoice({{ $sale->id }})"
                                                title="Imprimer"
                                            >
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a51.899 51.899 0 0 0-2.649-.762m.764 1.228a51.89 51.89 0 0 1-.764 1.228m0 0a51.89 51.89 0 0 1-.764 1.228M3 3c1.232 0 2.429.106 3.587.307M7.5 3.75A1.5 1.5 0 0 0 6.057 5.5m1.5 1.5A1.5 1.5 0 0 0 7.5 3.75m0 0V5.25m0 3h3.75m-3.75 0V9m0 3h3.75m-3.75 0v3.75m0-3.75h3.75m-3.75 0V15" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucune vente trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de détails - Version daisyUI -->
    @if($showDetailsModal)
    <div class="modal modal-open">
        <div class="modal-box max-w-4xl">
            
            <h3 class="font-bold text-lg">Détails de la vente #{{ $selectedSale?->id ?? '' }}</h3>
            @if($selectedSale)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <h3 class="font-semibold mb-2">Informations</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Date:</span> {{ $selectedSale->created_at->format('d/m/Y H:i') }}</p>
                            <p><span class="font-medium">Client:</span> {{ $selectedSale->client->nom ?? 'N/A' }}</p>
                            <p><span class="font-medium">Total:</span> {{ number_format($selectedSale->total, 0, ',', ' ') }} Fc</p>
                            <p><span class="font-medium">Vendeur:</span> {{ $selectedSale->user->name ?? 'N/A' }}</p>
                        </div>
                        
                        
                    </div>
                    
                    <div>
                        <div class="mt-6 p-4 bg-base-200 rounded-box">
                            <h3 class="font-semibold mb-2">Résumé</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Total produits:</span>
                                    <span>{{ number_format($selectedSale->total, 0, ',', ' ') }} Fc</span>
                                </div>
                                <div class="flex justify-between font-bold border-t pt-2">
                                    <span>Total:</span>
                                    <span>{{ number_format($selectedSale->total, 0, ',', ' ') }} Fc</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
                <h3 class="font-semibold mt-6 mb-2">Produits</h3>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedSale->details as $detail)
                                        <tr>
                                            <td>{{ $detail->produit->nom }}</td>
                                            <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} Fc</td>
                                            <td>{{ $detail->quantite }}</td>
                                            <td>{{ number_format($detail->prix_unitaire * $detail->quantite, 0, ',', ' ') }} Fc</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
            @endif
            
            <div class="modal-action">
                <button class="btn" wire:click="$set('showDetailsModal', false)">Fermer</button>
           
                @if($selectedSale)
                    <button class="btn btn-primary"  onclick="window.open('{{ route('ventes.print-invoice', ['vente' => $selectedSale->id]) }}', '_blank')">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a51.899 51.899 0 0 0-2.649-.762m.764 1.228a51.89 51.89 0 0 1-.764 1.228m0 0a51.89 51.89 0 0 1-.764 1.228M3 3c1.232 0 2.429.106 3.587.307M7.5 3.75A1.5 1.5 0 0 0 6.057 5.5m1.5 1.5A1.5 1.5 0 0 0 7.5 3.75m0 0V5.25m0 3h3.75m-3.75 0V9m0 3h3.75m-3.75 0v3.75m0-3.75h3.75m-3.75 0V15" />
                        </svg>
                        Imprimer
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif    
    <!-- Modal d'export -->
   
    @if($showExportModal)
    <div class="modal modal-open">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Exporter les ventes</h3>
            
            <div class="space-y-4">
                <!-- Sélection du format -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Format d'export</span>
                    </label>
                    <div class="flex gap-2">
                        <button class="btn flex-1 {{ $exportFormat === 'excel' ? 'btn-primary' : 'btn-outline' }}" 
                                wire:click="$set('exportFormat', 'excel')">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a51.899 51.899 0 0 0-2.649-.762m.764 1.228a51.89 51.89 0 0 1-.764 1.228m0 0a51.89 51.89 0 0 1-.764 1.228M3 3c1.232 0 2.429.106 3.587.307M7.5 3.75A1.5 1.5 0 0 0 6.057 5.5m1.5 1.5A1.5 1.5 0 0 0 7.5 3.75m0 0V5.25m0 3h3.75m-3.75 0V9m0 3h3.75m-3.75 0v3.75m0-3.75h3.75m-3.75 0V15" />
                            </svg>
                            Excel
                        </button>
                        <button class="btn flex-1 {{ $exportFormat === 'pdf' ? 'btn-primary' : 'btn-outline' }}" 
                                wire:click="$set('exportFormat', 'pdf')">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625a1.125 1.125 0 0 0-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            PDF
                        </button>
                    </div>
                </div>
    
                <!-- Période -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date de début</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" wire:model="exportStartDate">
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date de fin</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" wire:model="exportEndDate">
                    </div>
                </div>
    
                <!-- Options spécifiques au PDF -->
                @if($exportFormat === 'pdf')
                <div class="space-y-2">
                    
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" class="checkbox" wire:model="includeDetails">
                            <span class="label-text">Inclure le détail des ventes</span>
                        </label>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Orientation</span>
                        </label>
                        <select class="select select-bordered w-full" wire:model="pdfOrientation">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Paysage</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="modal-action">
                <button class="btn" wire:click="$set('showExportModal', false)">Annuler</button>
                <button class="btn btn-primary" wire:click="exportData">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Exporter
                </button>
            </div>
        </div>
    </div>
    @endif    
</div>