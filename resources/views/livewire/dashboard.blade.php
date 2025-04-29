<div class="min-h-full">
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-base-content">Tableau de Bord</h2>
            <div class="join">
                <button wire:click="changePeriod('week')" 
                    class="join-item btn {{ $period === 'week' ? 'btn-primary' : 'btn-ghost' }}">
                    Cette semaine
                </button>
                <button wire:click="changePeriod('month')" 
                    class="join-item btn {{ $period === 'month' ? 'btn-primary' : 'btn-ghost' }}">
                    Ce mois
                </button>
                <button wire:click="changePeriod('year')" 
                    class="join-item btn {{ $period === 'year' ? 'btn-primary' : 'btn-ghost' }}">
                    Cette année
                </button>
            </div>
        </div>
        
        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Carte Produits -->
            <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-primary p-3 text-primary-content">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Produits</h3>
                            <p class="text-2xl font-bold">{{ $totalProduits }}</p>
                            <div class="mt-1">
                                @livewire('taux-change-scroller')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte Clients -->
            <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-secondary p-3 text-secondary-content">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Clients</h3>
                            <p class="text-2xl font-bold">{{ $totalClients }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte Ventes -->
            <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-accent p-3 text-accent-content">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Ventes</h3>
                            <p class="text-2xl font-bold">{{ $totalVentes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte Bénéfice -->
            <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-success p-3 text-success-content">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Bénéfice</h3>
                            <p class="text-2xl font-bold">{{ number_format($profit, 2) }} Fc</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphiques et analyses -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Graphique des ventes -->
            <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                    <h3 class="card-title text-lg">Évolution des ventes</h3>
                    <div class="mt-4 h-64">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Top 5 des Produits -->
            <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                    <h3 class="card-title text-lg">Top 5 Produits Vendus</h3>
                    <div class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="table table-pin-cols">
                                <tbody>
                                    @foreach($topSellingProduits as $med)
                                    <tr>
                                        <td class="font-medium">{{ $med->nom }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-success">
                                                {{ $med->total_sold }} unités
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alertes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Produits à faible stock -->
            <div class="card bg-base-100 shadow-md border-l-4 border-error">
                <div class="card-body">
                    <h3 class="card-title text-lg text-error">Produits à faible stock</h3>
                    <div class="mt-4 max-h-64 overflow-y-auto">
                        @forelse($lowStockProduits as $med)
                        <div class="flex justify-between items-center py-3 border-b border-base-200">
                            <span class="font-medium">{{ $med->nom }}</span>
                            <span class="badge {{ $med->stock < 5 ? 'badge-error' : 'badge-warning' }}">
                                {{ $med->stock }} unités
                            </span>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            <span>Aucun Produit à faible stock</span>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Produits qui expirent bientôt -->
            <div class="card bg-base-100 shadow-md border-l-4 border-warning">
                <div class="card-body">
                    <h3 class="card-title text-lg text-warning">Produits expirant bientôt</h3>
                    <div class="mt-4 max-h-64 overflow-y-auto">
                        @forelse($expiringProduits as $med)
                        <div class="flex justify-between items-center py-3 border-b border-base-200">
                            <span class="font-medium">{{ $med->nom }}</span>
                            <span class="badge badge-warning">
                                Expire le {{ \Carbon\Carbon::parse($med->date_expiration)->format('d/m/Y') }}
                            </span>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            <span>Aucun Produit n'expire bientôt</span>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:load', function() {
            var salesData = @json($salesData);
            
            var labels = salesData.map(item => item.date);
            var data = salesData.map(item => item.amount);
            
            var ctx = document.getElementById('salesChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventes (Fc)',
                        data: data,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' Fc';
                                }
                            }
                        }
                    }
                }
            });
            
            Livewire.on('periodChanged', function() {
                chart.data.labels = @this.salesData.map(item => item.date);
                chart.data.datasets[0].data = @this.salesData.map(item => item.amount);
                chart.update();
            });
        });
    </script>
</div>