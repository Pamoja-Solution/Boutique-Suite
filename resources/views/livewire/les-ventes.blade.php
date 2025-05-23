<div class="">
    @include("gerant.nav")

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Header -->
    <div class="sticky top-0 z-50 bg-base-100 text-primary-content shadow-lg">
        <div class="container mx-auto flex justify-between items-center p-4">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-chart-line mr-2"></i>Les Statistiques
            </h1>
            <div class="flex gap-2">
                <button wire:click="$toggle('showFilters')" class="btn btn-secondary">
                    <i class="fas fa-filter mr-2"></i>Filtres
                </button>
                <button wire:click="exportToExcel" class="btn btn-success">
                    <i class="fas fa-file-excel mr-2"></i>Exporter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Filtres -->
@if($showFilters)
<div class="bg-secondary-content text-neutral-content p-6 shadow-xl">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Date de début</span>
            </label>
            <input type="date" wire:model.live="startDate" class="input input-bordered" 
                   value="{{ $startDate ?? now()->subMonth()->format('Y-m-d') }}">
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Date de fin</span>
            </label>
            <input type="date" wire:model.live="endDate" class="input input-bordered"
                   value="{{ $endDate ?? now()->format('Y-m-d') }}">
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Client</span>
            </label>
            <select wire:model.live="selectedClient" class="select select-bordered">
                <option value="">Tous les clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-control flex items-end">
            <button wire:click="resetFilters" class="btn btn-neutral w-full">
                <i class="fas fa-sync-alt mr-2"></i>Réinitialiser
            </button>
        </div>
    </div>
</div>
@endif
    
    <!-- Statistiques Principales -->
    <div class="container mx-auto p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Carte 1: Total des ventes -->
        <div class="card bg-accent">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm opacity-80">Chiffre d'affaires</div>
                        <div class="text-3xl font-bold mt-2">{{ number_format($this->totalSales, 2) }} Fc</div>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="{{ $this->salesTrend > 0 ? 'text-success' : 'text-error' }}">
                            {{ $this->salesTrend > 0 ? '↑' : '↓' }} {{ abs($this->salesTrend) }}%
                        </span>
                        <span class="ml-2 opacity-80">vs période précédente</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Carte 2: Nombre de ventes -->
        <div class="card bg-primary">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm opacity-80">Nombre de ventes</div>
                        <div class="text-3xl font-bold mt-2">{{ $this->salesCount }}</div>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="{{ $this->salesCountTrend > 0 ? 'text-success' : 'text-error' }}">
                            {{ $this->salesCountTrend > 0 ? '↑' : '↓' }} {{ abs($this->salesCountTrend) }}%
                        </span>
                        <span class="ml-2 opacity-80">vs période précédente</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Carte 4: Panier moyen -->
        <div class="card bg-secondary">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm opacity-80">Panier moyen</div>
                        <div class="text-3xl font-bold mt-2">{{ number_format($this->averageCart, 2) }} Fc</div>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="{{ $this->averageCartTrend > 0 ? 'text-success' : 'text-error' }}">
                            {{ $this->averageCartTrend > 0 ? '↑' : '↓' }} {{ abs($this->averageCartTrend) }}%
                        </span>
                        <span class="ml-2 opacity-80">vs période précédente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tableau des ventes -->
    <div class="container mx-auto p-4">
        <div class="card bg-base-200">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium flex items-center">
                        <i class="fas fa-list-ul mr-2 text-info"></i> Détails des ventes
                    </h3>
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model.live="search" placeholder="Rechercher..." class="input input-bordered">
                        <select wire:model.live="perPage" class="select select-bordered">
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
                                    ID
                                    @if($sortField === 'id')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </th>
                                <th>Client</th>
                                <th wire:click="sortBy('total')" class="cursor-pointer">
                                    Total
                                    @if($sortField === 'total')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('created_at')" class="cursor-pointer">
                                    Date
                                    @if($sortField === 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr class="hover">
                                <td class="text-primary">#{{ $sale->id }}</td>
                                <td>
                                    <div class="font-medium">{{ $sale->client->nom }}</div>
                                    <div class="text-sm opacity-50">{{ $sale->client->email }}</div>
                                </td>
                                <td class="text-success font-bold">{{ number_format($sale->total, 2) }} Fc</td>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button wire:click="showDetails({{ $sale->id }})" class="btn btn-outline btn-primary btn-sm text-info">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-outline btn-secondary btn-xm text-info" title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button wire:click="sendEmail({{ $sale->id }})" class="btn btn-outline btn-accent btn-xs text-success" title="Envoyer par email">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center opacity-50">
                                    Aucune vente trouvée
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm opacity-70">
                        Affichage de {{ $sales->firstItem() }} à {{ $sales->lastItem() }} sur {{ $sales->total() }} résultats
                    </div>
                    <div>
                        {{ $this->sales->links('livewire.futurist-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Détails de vente -->
    @if($showDetailsModal)
    <div class="modal modal-open">
        <div class="modal-box max-w-5xl p-0">
            <div class="bg-neutral text-neutral-content p-4">
                <h3 class="text-lg font-medium flex items-center">
                    <i class="fas fa-receipt mr-2 text-info"></i> Détails de la vente {{ $selectedSale->matricule ?? '' }}
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Informations Client -->
                    <div>
                        <h4 class="text-sm font-medium opacity-70 mb-2">INFORMATIONS CLIENT</h4>
                        <div class="bg-base-200 rounded-box p-4">
                            <div class="flex items-center mb-3">
                                
                                        <div class=" w-10 rounded-full ">
                                            <svg class="w-[48px] h-[48px] " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z" clip-rule="evenodd"/>
                                              </svg>
                                        </div>
                                <div class="ml-4">
                                    <p class="font-medium">{{ $selectedSale->client->nom ?? '' }}</p>
                                    <p class="text-sm opacity-50">{{ $selectedSale->client->email ?? 'Aucune adresse email' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-xs opacity-50">Téléphone</p>
                                    <p>{{ $selectedSale->client->telephone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs opacity-50">Adresse</p>
                                    <p>{{ $selectedSale->client->adresse ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Résumé de la vente -->
                    <div>
                        <h4 class="text-sm font-medium opacity-70 mb-2">RÉSUMÉ DE LA VENTE</h4>
                        <div class="bg-base-200 rounded-box p-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-xs opacity-50">Date</p>
                                    <p>{{ $selectedSale->created_at->format('d/m/Y H:i') ?? '' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs opacity-50">Référence</p>
                                    <p class="text-primary">#{{ $selectedSale->matricule ?? '' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs opacity-50">Méthode de paiement</p>
                                    <p>Carte bancaire</p>
                                </div>
                                <div>
                                    <p class="text-xs opacity-50">Statut</p>
                                    <span class="badge badge-success">Complétée</span>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-base-300">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm font-medium opacity-70">Total</p>
                                    <p class="text-xl font-bold text-success">{{ number_format($selectedSale->total ?? 0, 2) }} Fc</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="text-sm font-medium opacity-70 mb-2">Produits</h4>
                <div class="bg-base-200 rounded-box overflow-hidden">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedSale->details ?? [] as $detail)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                <i class="fa-solid fa-bowl-food"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium">{{ $detail->produit->nom }}</div>
                                            <div class="text-sm opacity-50">{{ $detail->medicament->categorie->nom ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($detail->prix_unitaire, 2) }} Fc</td>
                                <td>{{ $detail->quantite }}</td>
                                <td class="text-success font-bold">{{ number_format($detail->prix_unitaire * $detail->quantite, 2) }} Fc</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-action bg-base-200 p-4">
                <button wire:click="printInvoice({{ $selectedSale->id ?? '' }})" class="btn btn-info">
                    <i class="fas fa-print mr-2"></i> Imprimer
                </button>
                <button wire:click="sendEmail({{ $selectedSale->id ?? '' }})" class="btn btn-success">
                    <i class="fas fa-envelope mr-2"></i> Envoyer
                </button>
                <button wire:click="hideDetails" class="btn">Fermer</button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Graphiques et Tableaux -->
    <div class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
       <!-- Graphique des ventes -->
        <div class="card bg-base-200 lg:col-span-2">
            <div class="card-body">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2 text-info"></i> Évolution des ventes
                </h3>
                <div class="h-80">
                    <canvas wire:ignore id="salesChart"></canvas>
                </div>
            </div>
        </div>

        
        <!-- Top Produits -->
        <div class="card bg-base-200">
            <div class="card-body">
                <h3 class="card-title">
                    <i class="fas fa-star mr-2 text-warning"></i> Top 5 Produits
                </h3>
                <div class="space-y-4">
                    @foreach($this->topProduits as $medicament)
                    <div class="flex items-center">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                <i class="fas fa-pills"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <p class="font-medium">{{ $medicament->nom }}</p>
                                <p class="text-success">{{ $medicament->total_quantity }} unités</p>
                            </div>
                            <progress class="progress progress-primary w-full mt-1" 
                                      value="{{ $medicament->total_quantity }}" 
                                      max="{{ max($topProduits->max('total_quantity'), 1) }}"></progress>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

<script>
    let salesChart;
    
        function setupSalesChart(chartData) {
    const ctx = document.getElementById('salesChart');
    if (!ctx) {
        return;
    }
    
    
    if (salesChart) {
        salesChart.destroy();
    }
    
    // Vérifier si les données sont valides
    if (!chartData || !chartData.labels || !chartData.data || 
        chartData.labels.length === 0 || chartData.data.length === 0) {
        // Afficher un message dans le canvas
        const context = ctx.getContext('2d');
        context.font = '14px Arial';
        context.fillStyle = '#666';
        context.textAlign = 'center';
        context.fillText('Aucune donnée disponible pour la période sélectionnée', ctx.width/2, ctx.height/2);
        return;
    }
    
        salesChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Ventes (Fc)',
                    data: chartData.data || [],
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return `${context.dataset.label}: ${context.raw.toLocaleString()} Fc`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => `${value.toLocaleString()} Fc`
                        }
                    }
                }
            }
        });
    }
    
   
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé!');
        return;
    }
    
    setupSalesChart(@json($salesChartData));
});
    
    // Écoute des mises à jour Livewire
    document.addEventListener('livewire:initialized', () => {
    Livewire.on('updateSalesChart', (data) => {
        setupSalesChart(data);
    });
});
</script>
</div>


@push('scripts')

@endpush