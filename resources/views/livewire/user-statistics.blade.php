<div>
    @include('gerant.nav')
    @section("titre","Statistiques Générales")

    <div class="p-4 min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Statistiques des Vendeurs</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Liste des utilisateurs -->
            <div class="card bg-base-100 shadow hover:shadow-lg transition-all duration-300">
                <div class="card-body">
                    <h2 class="card-title">Utilisateurs</h2>
                    
                    <div class="mb-4">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Rechercher un utilisateur..." 
                            class="input input-bordered w-full"
                        >
                    </div>
                    
                    <div class="overflow-y-auto max-h-96 scrollbar-thin">
                        @foreach($users as $user)
                            <div 
                                wire:click="selectUser({{ $user->id }})"
                                class="p-3 mb-2 rounded-lg border bg-accent-200 border-primary cursor-pointer transition-all duration-200 hover:bg-base-200 {{ $selectedUser == $user->id ? 'bg-primary text-primary-content' : '' }}"
                            >
                                <div class="flex items-center">
                                    @if($user->image)
                                        <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-base-300 flex items-center justify-center mr-3">
                                            <span class="font-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-medium">{{ $user->name }}</h3>
                                        <p class="text-sm opacity-70">{{ $user->matricule }} - {{ ucfirst($user->role) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($users->isEmpty())
                            <div class="p-4 text-center opacity-70">
                                Aucun utilisateur trouvé
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Filtres et statistiques -->
            <div class="md:col-span-2 space-y-6">
                @if($selectedUser)
                    <div class="card bg-base-100 shadow hover:shadow-lg transition-all duration-300">
                        <div class="card-body">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="card-title">Période d'analyse</h2>
                                <button 
                                    wire:click="resetUserSelection"
                                    class="btn btn-sm btn-outline"
                                >
                                    Retour
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                                <button 
                                    wire:click="$set('dateRange', 'today')" 
                                    class="btn btn-sm  {{ $dateRange === 'today' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Aujourd'hui
                                </button>
                                <button 
                                    wire:click="$set('dateRange', 'yesterday')" 
                                    class="btn btn-sm {{ $dateRange === 'yesterday' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Hier
                                </button>
                                <button 
                                    wire:click="$set('dateRange', 'week')" 
                                    class="btn btn-sm {{ $dateRange === 'week' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Cette semaine
                                </button>
                                <button 
                                    wire:click="$set('dateRange', 'month')" 
                                    class="btn btn-sm {{ $dateRange === 'month' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Ce mois
                                </button>
                                <button 
                                    wire:click="$set('dateRange', 'year')" 
                                    class="btn btn-sm {{ $dateRange === 'year' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Cette année
                                </button>
                                <button 
                                    wire:click="$set('dateRange', 'custom')" 
                                    class="btn btn-sm {{ $dateRange === 'custom' ? 'btn-primary' : 'btn-outline' }}"
                                >
                                    Personnalisé
                                </button>
                            </div>
                            
                            @if($dateRange === 'custom')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                    <div>
                                        <label class="label">Date de début</label>
                                        <input 
                                            type="date" 
                                            wire:model.live="customStartDate" 
                                            class="input input-bordered w-full"
                                        >
                                    </div>
                                    <div>
                                        <label class="label">Date de fin</label>
                                        <input 
                                            type="date" 
                                            wire:model.live="customEndDate" 
                                            class="input input-bordered w-full"
                                        >
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card bg-base-100 shadow hover:shadow-lg transition-all duration-300">
                        <div class="card-body">
                            <h2 class="card-title">{{ $userStats['user']->name }}</h2>
                            <p class="opacity-70 mb-6">
                                Période d'analyse: {{ $userStats['periode']['debut'] }} au {{ $userStats['periode']['fin'] }}
                            </p>
                            
                            <!-- Résumé des performances -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="stats bg-primary text-primary-content">
                                    <div class="stat">
                                        <div class="stat-title opacity-90">Total des ventes</div>
                                        <div class="stat-value">{{ $userStats['ventes']['total'] }}</div>
                                        <div class="stat-desc">{{ number_format($userStats['ventes']['moyenne_par_jour'], 1) }} ventes/jour</div>
                                    </div>
                                </div>
                                
                                <div class="stats bg-success text-success-content">
                                    <div class="stat">
                                        <div class="stat-title">Montant total</div>
                                        <div class="stat-value">{{ number_format($userStats['ventes']['montant'], 0, ',', ' ') }} FC</div>
                                        <div class="stat-desc">{{ number_format($userStats['ventes']['montant_moyen_par_jour'], 0, ',', ' ') }} FC/jour</div>
                                    </div>
                                </div>
                                
                                <div class="stats {{ $userStats['finances']['balance'] >= 0 ? 'bg-success' : 'bg-error' }} text-primary-content">
                                    <div class="stat">
                                        <div class="stat-title">Balance financière</div>
                                        <div class="stat-value">{{ number_format($userStats['finances']['balance'], 0, ',', ' ') }} FC</div>
                                        <div class="stat-desc">Revenus - Dépenses</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Liste des ventes -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-3">Ventes récentes</h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="table table-zebra">
                                        <thead>
                                            <tr>
                                                <th wire:click="sortBy('id')" class="cursor-pointer">
                                                    ID
                                                    @if($sortField === 'id')
                                                        @if($sortDirection === 'asc')
                                                            ↑
                                                        @else
                                                            ↓
                                                        @endif
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('created_at')" class="cursor-pointer">
                                                    Date
                                                    @if($sortField === 'created_at')
                                                        @if($sortDirection === 'asc')
                                                            ↑
                                                        @else
                                                            ↓
                                                        @endif
                                                    @endif
                                                </th>
                                                <th>Client</th>
                                                <th wire:click="sortBy('total')" class="cursor-pointer">
                                                    Montant
                                                    @if($sortField === 'total')
                                                        @if($sortDirection === 'asc')
                                                            ↑
                                                        @else
                                                            ↓
                                                        @endif
                                                    @endif
                                                </th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sales as $sale)
                                                <tr wire:key="sale-{{ $sale->id }}-{{ time() }}">
                                                    <td>#{{ $sale->id }}</td>
                                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($sale->client)
                                                            {{ $sale->client->nom }}
                                                        @else
                                                            Non spécifié
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($sale->total, 0, ',', ' ') }} FC</td>
                                                    <td>
                                                        <div class="flex space-x-2">
                                                            <button 
                                                                wire:click="showDetails({{ $sale->id }})" 
                                                                class="btn btn-sm btn-info"
                                                            >
                                                                Détails
                                                            </button>
                                                            <button 
                                                                 onclick="window.open('{{ route('ventes.print-invoice', ['vente' => $sale->id]) }}', '_blank')"
                                                                class="btn btn-sm btn-secondary"
                                                            >
                                                                Imprimer
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-4">
                                    {{ $sales->links() }}
                                </div>
                            </div>
                            
                            <!-- Top produits vendus -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-3">Top 5 produits vendus</h3>
                                @if(count($userStats['produits']['top']) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="table table-zebra">
                                            <thead>
                                                <tr>
                                                    <th>Produit</th>
                                                    <th>Référence</th>
                                                    <th>Quantité</th>
                                                    <th>Montant</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($userStats['produits']['top'] as $produit)
                                                    <tr>
                                                        <td>{{ $produit->nom }}</td>
                                                        <td>{{ $produit->reference_interne }}</td>
                                                        <td>{{ $produit->total_quantity }}</td>
                                                        <td>{{ number_format($produit->total_amount, 2, ',', ' ') }} FC</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <span>Aucun produit vendu sur cette période</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Évolution des ventes -->
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Évolution des ventes</h3>
                                @if(count($userStats['ventes']['evolution']) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="table table-zebra">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Nombre de ventes</th>
                                                    <th>Montant total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($userStats['ventes']['evolution'] as $evolution)
                                                    <tr>
                                                        <td>{{ $evolution->date }}</td>
                                                        <td>{{ $evolution->count }}</td>
                                                        <td>{{ number_format($evolution->total_amount, 2, ',', ' ') }} FC</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <span>Aucune vente sur cette période</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card bg-base-100 shadow hover:shadow-lg transition-all duration-300">
                        <div class="card-body text-center">
                            <div class="text-primary mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="card-title justify-center">Sélectionnez un utilisateur</h2>
                            <p class="opacity-70">Veuillez sélectionner un utilisateur dans la liste pour afficher ses statistiques détaillées.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Modal de détails de vente -->
        @if($showDetailsModal)
            <div class="modal modal-open" wire:ignore.self>
                <div class="modal-box max-w-4xl"  @if($selectedSale) id="modal-{{ $selectedSale->id }}" @endif>
                    <h3 class="font-bold text-lg">Détails de la vente #{{ $selectedSale->id }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <h4 class="font-semibold">Informations générales</h4>
                            <div class="space-y-2 mt-2">
                                <p><span class="font-medium">Date:</span> {{ $selectedSale->created_at->format('d/m/Y H:i') }}</p>
                                <p><span class="font-medium">Vendeur:</span> {{ $selectedSale->user->name }}</p>
                                @if($selectedSale->client)
                                    <p><span class="font-medium">Client:</span> {{ $selectedSale->client->nom }}</p>
                                    <p><span class="font-medium">Téléphone:</span> {{ $selectedSale->client->telephone ?? 'Non spécifié' }}</p>
                                @endif
                                <p><span class="font-medium">Montant total:</span> {{ number_format($selectedSale->total, 0, ',', ' ') }} FC</p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold">Articles vendus</h4>
                            <div class="overflow-x-auto mt-2">
                                <table class="table table-zebra table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Qté</th>
                                            <th>Prix unit.</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedSale->details as $detail)
                                            <tr>
                                                <td>{{ $detail->produit->nom }}</td>
                                                <td>{{ $detail->quantite }}</td>
                                                <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FC</td>
                                                <td>{{ number_format($detail->quantite * $detail->prix_unitaire, 0, ',', ' ') }} FC</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-action">
                        <button wire:click="hideDetails" class="btn">Fermer</button>
                        <button  onclick="window.open('{{ route('ventes.print-invoice', ['vente' => $selectedSale->id]) }}', '_blank')" class="btn btn-primary">
                            Imprimer la facture
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-modal', () => {
            // Cette fonction force le re-rendu du modal
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.classList.remove('modal-open');
                setTimeout(() => modal.classList.add('modal-open'), 10);
            });
        });
    });
</script>
@endpush
</div>