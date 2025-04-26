<div>
    
    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-900">Tableau de Bord</h2>
            
            <!-- Période -->
            <div class="my-4">
                <div class="flex space-x-4">
                    <button wire:click="changePeriod('week')" 
                        class="px-4 py-2 rounded-md {{ $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-200' }}">
                        Cette semaine
                    </button>
                    <button wire:click="changePeriod('month')" 
                        class="px-4 py-2 rounded-md {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-200' }}">
                        Ce mois
                    </button>
                    <button wire:click="changePeriod('year')" 
                        class="px-4 py-2 rounded-md {{ $period === 'year' ? 'bg-indigo-600 text-white' : 'bg-gray-200' }}">
                        Cette année
                    </button>
                </div>
            </div>
            
            <!-- Statistiques générales -->
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Produits</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $totalProduits }}
                                            @livewire('taux-change-scroller')

                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Clients</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $totalClients }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Ventes</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $totalVentes }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Bénéfice</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ number_format($profit, 2) }} Fc</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphiques et analyses -->
            <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <!-- Graphique des ventes -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Évolution des ventes</h3>
                        <div class="mt-2 h-64">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Top 5 des Produits les plus vendus -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Top 5 Produits Vendus</h3>
                        <div class="mt-4">
                            <ul class="divide-y divide-gray-200">
                                @foreach($topSellingProduits as $med)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $med->nom }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $med->total_sold }} unités
                                        </span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertes -->
            <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <!-- Produits à faible stock -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-red-600">Produits à faible stock</h3>
                        <div class="mt-4 max-h-64 overflow-y-auto">
                            <ul class="divide-y divide-gray-200">
                                @forelse($lowStockProduits as $med)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $med->nom }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $med->stock < 5 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $med->stock }} unités restantes
                                        </span>
                                    </div>
                                </li>
                                @empty
                                <li class="py-3 text-sm text-gray-500">Aucun médicament à faible stock</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Produits qui expirent bientôt -->
                <!--div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-orange-600">Produits expirant bientôt</h3>
                        <div class="mt-4 max-h-64 overflow-y-auto">
                            <ul class="divide-y divide-gray-200">
                                @ forelse($expiringProduits as $med)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{ { $med->nom }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Expire le { { \Carbon\Carbon::parse($med->date_expiration)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </li>
                                @ empty
                                <li class="py-3 text-sm text-gray-500">Aucun médicament n'expire bientôt</li>
                                @ endforelse
                            </ul>
                        </div>
                    </div>
                </div-->
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
