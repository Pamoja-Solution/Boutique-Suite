<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div class="futurist-container bg-gray-900 text-gray-100 min-h-screen ">
        <!-- Header Futuriste -->
        <div class=" fixed top-0 start-0 z-50 futurist-header bg-indigo-900 p-6 shadow-lg border-b border-gray-500 border-2xl fixed top sticky">
            <div class="max-w-7xl mx-auto flex justify-between items-center ">
                <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-500 ">
                    <i class="fas fa-chart-line mr-2"></i>Mes Statistiques
                </h1>
                <div class="flex space-x-4">
                    <button wire:click="$toggle('showFilters')" class="futurist-btn px-3 py-2 rounded rounded-lg bg-indigo-700 hover:bg-indigo-600">
                        <i class="fas fa-filter mr-2"></i>Filtres
                    </button>
                    <button wire:click="exportToExcel" class="futurist-btn bg-green-700 hover:bg-green-600 px-3 py-2 rounded rounded-lg">
                        <i class="fas fa-file-excel mr-2"></i>Exporter
                    </button>
                </div>
            </div>
        </div>
    
        <!-- Filtres Futuristes -->
        @if($showFilters)
        <div class="futurist-filters bg-gray-800 p-6 shadow-xl border-b border-indigo-700">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-indigo-300 mb-1">Date de début</label>
                    <input type="date" wire:model.live="startDate" class="bg-gray-800 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-indigo-300 mb-1">Date de fin</label>
                    <input type="date" wire:model.live="endDate" class="bg-gray-800 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-indigo-300 mb-1">Client</label>
                    <select wire:model.live="selectedClient" class="bg-gray-800 rounded">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="resetFilters" class="futurist-btn py-3 rounded rounded-full bg-gray-700 hover:bg-gray-600 w-full">
                        <i class="fas fa-sync-alt mr-2"></i>Réinitialiser
                    </button>
                </div>
            </div>
        </div>
        @endif
    
        <!-- Statistiques Principales -->
        <div class="futurist-stats py-6">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Carte 1: Total des ventes -->
                <div class="futurist-card bg-gradient-to-br from-purple-900 to-indigo-800 rounded rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-indigo-200">Chiffre d'affaires</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($this->totalSales, 2) }} Fc</p>
                            </div>
                            <div class="">
                                <i class="fas fa-euro-sign"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-indigo-200">
                                <span class="{{ $this->salesTrend > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $this->salesTrend > 0 ? '↑' : '↓' }} {{ abs($this->salesTrend) }}%
                                </span>
                                <span class="ml-2">vs période précédente</span>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Carte 2: Nombre de ventes -->
                <div class="futurist-card bg-gradient-to-br from-blue-900 to-cyan-800 rounded rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-cyan-200">Nombre de ventes</p>
                                <p class="text-3xl font-bold mt-2">{{ $this->salesCount }}</p>
                            </div>
                            <div class="futurist-icon ">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-cyan-200">
                                <span class="{{ $this->salesCountTrend > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $this->salesCountTrend > 0 ? '↑' : '↓' }} {{ abs($this->salesCountTrend) }}%
                                </span>
                                <span class="ml-2">vs période précédente</span>
                            </div>
                        </div>
                    </div>
                </div>
    
                
    
                <!-- Carte 4: Panier moyen -->
                <div class="futurist-card bg-gradient-to-br from-red-900 to-pink-800 rounded rounded-lg ">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-pink-200">Panier moyen</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($this->averageCart, 2) }} Fc</p>
                            </div>
                            <div class="futurist-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-pink-200">
                                <span class="{{ $this->averageCartTrend > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $this->averageCartTrend > 0 ? '↑' : '↓' }} {{ abs($this->averageCartTrend) }}%
                                </span>
                                <span class="ml-2">vs période précédente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        
    
        <!-- Tableau des ventes -->
        <div class="max-w-7xl mx-auto px-6 pb-6 ">
            <div class="futurist-card bg-gray-800 overflow-hidden rounded rounded-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-100 flex items-center">
                            <i class="fas fa-list-ul mr-2 text-blue-400"></i> Détails des ventes
                        </h3>
                        <div class="flex items-center space-x-2">
                            <input type="text" wire:model.live="search" placeholder="Rechercher..." class="bg-gray-800 border-gray-600 rounded rounded-ld">
                            <select wire:model.live="perPage" class="bg-gray-800 border-gray-600 rounded rounded-ld">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('id')">
                                        ID
                                        @if($sortField === 'id')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-500"></i>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Client
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('total')">
                                        Total
                                        @if($sortField === 'total')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-500"></i>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                        Date
                                        @if($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-500"></i>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Détails
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @forelse($sales as $sale)
                                <tr class="hover:bg-gray-750 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-400">
                                        #{{ $sale->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-100">{{ $sale->client->nom }}</div>
                                        <div class="text-xs text-gray-400">{{ $sale->client->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400 font-bold">
                                        {{ number_format($sale->total, 2) }} Fc
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $sale->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        <button wire:click="showDetails({{ $sale->id }})" class="text-indigo-400 hover:text-indigo-300">
                                            <i class="fas fa-eye mr-1"></i> Voir
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        <div class="flex space-x-2">
                                            <button wire:click="printInvoice({{ $sale->id }})" class="text-blue-400 hover:text-blue-300" title="Imprimer">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            
                                            <button wire:click="sendEmail({{ $sale->id }})" class="text-green-400 hover:text-green-300" title="Envoyer par email">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-400">
                                        Aucune vente trouvée
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
    
                    <div class="mt-4 px-6 py-3 flex items-center justify-between border-t border-gray-700">
                        <div class="text-sm text-gray-400">
                            Affichage de <span class="font-medium">{{ $sales->firstItem() }}</span> à <span class="font-medium">{{ $sales->lastItem() }}</span> sur <span class="font-medium">{{ $sales->total() }}</span> résultats
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
        <div class="fixed inset-0 z-50 overflow-y-auto rounded rounded-2xl">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-90" wire:click="hideDetails"></div>
                </div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-medium text-gray-100 flex items-center">
                            <i class="fas fa-receipt mr-2 text-indigo-400"></i> Détails de la vente {{ $selectedSale->matricule ?? '' }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-400 mb-2">INFORMATIONS CLIENT</h4>
                                <div class="bg-gray-750 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-900 flex items-center justify-center">
                                            <i class="fas fa-user text-indigo-400"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-100">{{ $selectedSale->client->nom ?? '' }}</p>
                                            <p class="text-xs text-gray-400">{{ $selectedSale->client->email ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-300">
                                        <div>
                                            <p class="text-xs text-gray-400">Téléphone</p>
                                            <p>{{ $selectedSale->client->telephone ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Adresse</p>
                                            <p>{{ $selectedSale->client->adresse ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             	

                            <div>
                                <h4 class="text-sm font-medium text-gray-400 mb-2">RÉSUMÉ DE LA VENTE</h4>
                                <div class="bg-gray-750 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-xs text-gray-400">Date</p>
                                            <p class="text-gray-100">{{ $selectedSale->created_at->format('d/m/Y H:i') ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Référence</p>
                                            <p class="text-indigo-400">#{{ $selectedSale->matricule ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Méthode de paiement</p>
                                            <p class="text-gray-100">Carte bancaire</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Statut</p>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-900 text-green-300">Complétée</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <p class="text-sm font-medium text-gray-400">Total</p>
                                            <p class="text-xl font-bold text-green-400">{{ number_format($selectedSale->total ?? 0, 2) }} Fc</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-sm font-medium text-gray-400 mb-2">Produits</h4>
                        <div class="bg-gray-750 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Produit
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Prix unitaire
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Quantité
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($selectedSale->details ?? [] as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-900 rounded-full flex items-center justify-center">
                                                    <i class="fa-solid fa-bowl-food"></i> 
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-100">{{ $detail->produit->nom }}</div>
                                                    <div class="text-xs text-gray-400">{{ $detail->medicament->categorie->nom ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ number_format($detail->prix_unitaire, 2) }} Fc
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $detail->quantite }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400 font-bold">
                                            {{ number_format($detail->prix_unitaire * $detail->quantite, 2) }} Fc
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="bg-gray-750 px-6 py-4 flex justify-end space-x-3">
                        <button wire:click="printInvoice({{ $selectedSale->id ?? '' }})" class="futurist-btn bg-blue-700 px-2 py-2 rounded rounded-lg hover:bg-blue-600">
                            <i class="fas fa-print mr-2"></i> Imprimer
                        </button>
                        <button wire:click="sendEmail({{ $selectedSale->id ?? '' }})" class="futurist-btn bg-green-700 px-2 py-2 rounded rounded-lg hover:bg-green-600">
                            <i class="fas fa-envelope mr-2"></i> Envoyer par email
                        </button>
                        <button wire:click="hideDetails" class="futurist-btn bg-gray-700 px-2 py-2 rounded rounded-lg hover:bg-gray-600">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <!-- Graphiques et Tableaux -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 px-6 pb-6">
            <!-- Graphique des ventes -->
            <div class="futurist-card bg-gray-800 lg:col-span-2">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-indigo-400"></i> Évolution des ventes
                    </h3>
                    <div class="h-80 w-full relative">
                        <div style="height: 300px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Top Produits -->
            <div class="futurist-card bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-star mr-2 text-yellow-400"></i> Top 5 Produits
                    </h3>
                    <div class="space-y-4">
                        @foreach($this->topProduits as $medicament)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-900 flex items-center justify-center">
                                <i class="fas fa-pills text-indigo-400"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-gray-100">{{ $medicament->nom }}</p>
                                    <p class="text-sm text-green-400">{{ $medicament->total_quantity }} unités</p>
                                </div>
                                <div class="mt-1">
                                    <div class="h-1 w-full bg-gray-700 rounded-full">
                                        <div class="h-1 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full" 
                                             style="width: {{ ($medicament->total_quantity / max($topProduits->max('total_quantity'), 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <style>
        .futurist-container {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .futurist-btn {
            @apply px-4 py-2 rounded-lg transition-all duration-200 flex items-center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .futurist-input {
            @apply w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500;
        }
        
        .futurist-card {
            @apply rounded-xl shadow-lg border border-gray-700 transition-all duration-300 hover:shadow-xl;
        }
        
        .futurist-icon {
            @apply h-12 w-12 rounded-full flex items-center justify-center text-xl;
        }
        
        /* Animation pour les cartes de stats */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        
        .futurist-card:hover {
            animation: pulse 2s infinite;
        }
    </style>
    @endpush
    
   
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let salesChart;
        
        // Fonction pour initialiser ou mettre à jour le graphique
        function setupChart(chartData) {
            console.log('Configuration du graphique avec les données:', chartData);
            
            const salesCtx = document.getElementById('salesChart');
            if (!salesCtx) {
                console.error('Canvas #salesChart non trouvé');
                return;
            }
            
            // Détruire le graphique existant s'il y en a un
            if (salesChart) {
                salesChart.destroy();
            }
            
            // Créer un nouveau graphique
            salesChart = new Chart(salesCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Ventes (Fc)',
                        data: chartData.data,
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(255, 255, 255, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(31, 41, 55, 0.9)',
                            titleColor: 'rgba(209, 213, 219, 1)',
                            bodyColor: 'rgba(209, 213, 219, 1)',
                            borderColor: 'rgba(55, 65, 81, 1)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return 'Ventes: ' + context.parsed.y.toFixed(2) + ' Fc';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: 'rgba(156, 163, 175, 1)'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(55, 65, 81, 1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: 'rgba(156, 163, 175, 1)',
                                callback: function(value) {
                                    return value + ' Fc';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Initialisation avec les données initiales
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer les données initiales (si disponibles)
            try {
                const initialData = @json($salesChartData);
                console.log('Données initiales:', initialData);
                
                if (initialData && initialData.labels && initialData.data) {
                    setupChart(initialData);
                } else {
                    console.warn('Données initiales non disponibles ou incomplètes');
                }
            } catch (error) {
                console.error('Erreur lors de l\'initialisation:', error);
            }
        });
        
        // Écouter les mises à jour de Livewire
        document.addEventListener('livewire:initialized', function() {
            // Dans Livewire 3, utilisez cette syntaxe pour les événements
            Livewire.on('updateSalesChart', (newData) => {
                console.log('Événement updateSalesChart reçu:', newData);
                setupChart(newData);
            });
        });
    </script>
@endpush
</div>
    
    