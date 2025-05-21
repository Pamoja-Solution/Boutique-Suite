<div class="">
    @include('gerant.nav')

    <div class="p-6 bg-base-100 rounded-box shadow-sm">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Rapports de Ventes</h2>
        </div>
    
        <!-- Filtres -->
        <div class="bg-base-200 p-4 rounded-box mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Type de rapport -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Type de rapport</span>
                    </label>
                    <select class="select select-bordered" wire:model.live="reportType">
                        <option value="daily">Journalier</option>
                        <option value="monthly">Mensuel</option>
                        <option value="custom">Intervalle personnalisé</option>
                    </select>
                </div>
    
                <!-- Date journalière -->
                <div class="form-control" x-data x-show="$wire.reportType === 'daily'">
                    <label class="label">
                        <span class="label-text">Date</span>
                    </label>
                    <input type="date" class="input input-bordered" wire:model.live="startDate">
                </div>
    
                <!-- Mois pour mensuel -->
                <div class="form-control" x-data x-show="$wire.reportType === 'monthly'">
                    <label class="label">
                        <span class="label-text">Mois</span>
                    </label>
                    <input type="month" class="input input-bordered" wire:model.live="startDate">
                </div>
    
                <!-- Intervalle de dates -->
                <template x-if="$wire.reportType === 'custom'">
                    <div class="col-span-2 grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Date de début</span>
                            </label>
                            <input type="date" class="input input-bordered" wire:model.live="startDate">
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Date de fin</span>
                            </label>
                            <input type="date" class="input input-bordered" wire:model.live="endDate">
                        </div>
                    </div>
                </template>
    
                <!-- Sélection du vendeur -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Vendeur</span>
                    </label>
                    <select class="select select-bordered" wire:model.live="selectedUserId">
                        <option value="all">Tous les vendeurs</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
    
            <div class="mt-4 flex justify-between items-center">
                <button class="btn btn-error" wire:click="resetFilters">
                    <i class="fas fa-times-circle mr-2"></i> Réinitialiser les filtres
                </button>
                
                <!-- Bouton d'export (si vous le gardez) -->
                <button class="btn btn-primary" wire:click="$toggle('showExportModal')">
                    <i class="fas fa-file-export mr-2"></i> Exporter
                </button>
            </div>
        </div>
    
        <!-- Résumé -->
        <div class="stats shadow bg-base-100 mb-6">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <i class="fas fa-shopping-cart text-3xl"> </i>
                </div>
                <div class="stat-title">Total des ventes</div>
                <div class="stat-value text-primary">{{ number_format($this->salesSummary, 2) }} FC</div>
                <div class="stat-desc">
                    @if($reportType === 'daily')
                        Pour le {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                    @elseif($reportType === 'monthly')
                        Pour {{ Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
                    @else
                        Du {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @endif
                </div>
            </div>
    
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <i class="fas fa-users" > </i>
                </div>
                <div class="stat-title">Nombre de ventes</div>
                <div class="stat-value text-secondary">{{ $this->sales->total() }}</div>
                <div class="stat-desc">
                    @if($selectedUserId === 'all')
                        Tous les vendeurs
                    @else
                        Vendeur: {{ $this->users->firstWhere('id', $selectedUserId)?->name }}
                    @endif
                </div>
            </div>
        </div>
    
        <!-- Performance par vendeur -->
        @if($selectedUserId === 'all')
            <div class="bg-base-200 p-4 rounded-box mb-6">
                <h3 class="text-lg font-semibold mb-4">Performance par vendeur</h3>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Vendeur</th>
                                <th class="text-right">Nombre de ventes</th>
                                <th class="text-right">Total des ventes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->salesBySeller as $seller)
                                <tr>
                                    <td>{{ $seller['name'] }}</td>
                                    <td class="text-right">{{ $seller['sales_count'] }}</td>
                                    <td class="text-right">{{ number_format($seller['total'], 2) }} FC</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    
        <!-- Liste des ventes -->
        <div class="bg-base-200 p-4 rounded-box mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Détail des ventes</h3>
                <button class="btn btn-sm btn-primary" wire:click="$toggle('showExportModal')">
                    <i class="fas fa-file-export mr-2"> </i> Exporter
                </button>
            </div>
    
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>N° Vente</th>
                            <th>Client</th>
                            <th>Vendeur</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->sales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->matricule }}</td>
                                <td>{{ $sale->client->nom }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td class="text-right">{{ number_format($sale->total, 2) }} FC</td>
                                <td class="text-right">
                                    <button wire:click="showDetails({{ $sale->id }})" class="btn btn-xs btn-primary">
                                        <i class="fas fa-eye"></i> Détails
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune vente trouvée pour les critères sélectionnés</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            <div class="mt-4">
                {{ $this->sales->links() }}
            </div>
        </div>
    
        <!-- Modal d'export -->
        @if($showExportModal)
            <div class="modal modal-open">
                <div class="modal-box max-w-2xl p-0">
                    <div class="bg-neutral text-neutral-content p-4">
                        <h3 class="text-lg font-medium flex items-center">
                            <i class="fas fa-file-export mr-2 text-info"></i> Exporter le rapport
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Format d'export</span>
                            </label>
                            <select class="select select-bordered w-full" wire:model="exportType">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
    
                        @if($exportType === 'pdf')
                        <div class="space-y-4">
                            
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-4">
                                    <input type="checkbox" class="checkbox" wire:model="includeDetails" checked />
                                    <span class="label-text">Inclure les détails des ventes</span>
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Orientation</span>
                                </label>
                                <select class="select select-bordered" wire:model="pdfOrientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape">Paysage</option>
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="modal-action bg-base-200 p-4">
                        <button wire:click="$set('showExportModal', false)" class="btn btn-error">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </button>
                        <button wire:click="exportReport" class="btn btn-primary">
                            <i class="fas fa-download mr-2"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>
            @endif
    
    
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
                                        <div class="w-10 rounded-full">
                                            <svg class="w-[48px] h-[48px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
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
                                            <p class="text-xs opacity-50">Vendeur</p>
                                            <p>{{ $selectedSale->user->name ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs opacity-50">Statut</p>
                                            <span class="badge badge-success">Complétée</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-base-300">
                                        <div class="flex justify-between items-center">
                                            <p class="text-sm font-medium opacity-70">Total</p>
                                            <p class="text-xl font-bold text-success">{{ number_format($selectedSale->total ?? 0, 2) }} FC</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-sm font-medium opacity-70 mb-2">PRODUITS</h4>
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
                                                        <i class="fa-solid fa-box"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium">{{ $detail->produit->nom }}</div>
                                                    <div class="text-sm opacity-50">{{ $detail->produit->reference_interne }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($detail->prix_unitaire, 2) }} FC</td>
                                        <td>{{ $detail->quantite }}</td>
                                        <td class="text-success font-bold">{{ number_format($detail->prix_unitaire * $detail->quantite, 2) }} FC</td>
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
                        <button wire:click="hideDetails" class="btn btn-error" >Fermer</button>
                    </div>
                </div>
            </div>
            @endif
    </div>
</div>