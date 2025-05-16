@section("titre",'Gestion des stocks')
<div class="">
    @include('gerant.nav')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl rounded-box p-6">
                <h1 class="text-2xl font-bold mb-6">Gestion des stocks</h1>
                
                <!-- Barre de recherche -->
                <div class="mb-6">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Rechercher un produit..."
                        class="input input-bordered w-full"
                    >
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Liste des produits -->
                    <div class="md:col-span-2">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Référence</th>
                                        <th>Stock</th>
                                        <th>Prix</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produits as $produit)
                                        <tr @class([
                                            'bg-error/10' => $produit->stock <= $produit->seuil_alerte,
                                            'hover' => true
                                        ]) wire:key="product-card-{{ $produit->id }}-{{ $loop->index }}">
                                            <td>
                                                <div class="font-medium">{{ $produit->nom }}</div>
                                                @if($produit->date_expiration)
                                                    <div class="text-xs text-gray-500">
                                                        Exp: {{ $produit->date_expiration->format('d/m/Y') }}
                                                        @if($produit->date_expiration < now()->addDays(30))
                                                            <span class="text-error">(Bientôt expiré)</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-sm text-gray-500">
                                                {{ $produit->reference_interne }}
                                            </td>
                                            <td>
                                                <span @class([
                                                    'badge',
                                                    'badge-success' => $produit->stock > $produit->seuil_alerte,
                                                    'badge-error' => $produit->stock <= $produit->seuil_alerte
                                                ])>
                                                    {{ $produit->stock }} {{ $produit->unite_mesure }}
                                                </span>
                                            </td>
                                            <td class="text-sm text-gray-500">
                                                {{ number_format($produit->prix_vente, 2) }} Fc
                                            </td>
                                            <td class="text-sm font-medium">
                                                <button 
                                                    wire:click="selectionnerProduit({{ $produit->id }})"
                                                    class="text-primary hover:text-primary-focus"
                                                >
                                                    Sélectionner
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500">
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

                    <!-- Panneau de gestion du stock -->
                    <div class="md:col-span-1">
                        @if($produitSelectionne)
                            <div class="bg-base-200 p-4 rounded-box border border-base-300">
                                <h2 class="text-xl font-semibold mb-4">Gestion du stock</h2>
                                
                                <div class="mb-4">
                                    <h3 class="font-medium">{{ $produitSelectionne->nom }}</h3>
                                    <p class="text-sm text-gray-600">Ref: {{ $produitSelectionne->reference_interne }}</p>
                                    <p class="text-lg font-bold mt-2">
                                        Stock actuel: 
                                        <span @class([
                                            'text-success' => $produitSelectionne->stock > $produitSelectionne->seuil_alerte,
                                            'text-error' => $produitSelectionne->stock <= $produitSelectionne->seuil_alerte
                                        ])>
                                            {{ $produitSelectionne->stock }} {{ $produitSelectionne->unite_mesure }}
                                        </span>
                                    </p>
                                </div>

                                <form wire:submit.prevent="calculerStock">
                                    <div class="space-y-4">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Quantité à ajouter/retirer</span>
                                            </label>
                                            <input 
                                                wire:model="quantiteAjout" 
                                                type="number" 
                                                class="input input-bordered"
                                                required
                                            >
                                            <label class="label">
                                                <span class="label-text-alt">Entrez une valeur négative pour retirer du stock</span>
                                            </label>
                                        </div>

                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Nouveau prix de vente (optionnel)</span>
                                            </label>
                                            <input 
                                                wire:model="nouveauPrixVente" 
                                                type="number" 
                                                min="0" 
                                                step="0.01"
                                                class="input input-bordered"
                                                placeholder="Laisser vide pour garder {{ number_format($produitSelectionne->prix_vente, 2) }} Fc"
                                                value=""
                                            >
                                        </div>
                    
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Nouveau prix d'achat (optionnel)</span>
                                            </label>
                                            <input 
                                                wire:model="nouveauPrixAchat" 
                                                type="number" 
                                                min="0" 
                                                step="0.01"
                                                class="input input-bordered"
                                                placeholder="Laisser vide pour garder {{ number_format($produitSelectionne->prix_achat, 2) }} Fc"
                                                value=""
                                            >
                                        </div>

                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Date d'expiration (optionnel)</span>
                                            </label>
                                            <input 
                                                wire:model="nouvelleDateExpiration" 
                                                type="date" 
                                                class="input input-bordered"
                                                value="{{ $nouvelleDateExpiration ?? '' }}"
                                            >
                                            @if($produitSelectionne->date_expiration)
                                                <label class="label">
                                                    <span class="label-text-alt">
                                                        Actuelle: {{ is_string($produitSelectionne->date_expiration) 
                                                            ? \Carbon\Carbon::parse($produitSelectionne->date_expiration)->format('d/m/Y')
                                                            : $produitSelectionne->date_expiration->format('d/m/Y') }}
                                                    </span>
                                                </label>
                                            @endif
                                        </div>

                                        <button 
                                            type="submit"
                                            class="btn btn-primary w-full"
                                        >
                                            Calculer le nouveau stock
                                        </button>
                                    </div>
                                </form>

                                @if($nouveauStockCalcule !== null)
                                    <div class="mt-6 p-4 bg-info/10 rounded-box border border-info/20">
                                        <h3 class="font-medium text-lg mb-2">Récapitulatif des modifications</h3>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-2">
                                            <div>
                                                <p class="text-sm text-gray-600">Stock actuel</p>
                                                <p class="font-medium">{{ $stockActuel }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Nouveau stock</p>
                                                <p class="font-medium text-info">
                                                    {{ $nouveauStockCalcule }}
                                                </p>
                                            </div>
                                        </div>

                                        @if(!empty($nouveauPrixVente) && $nouveauPrixVente != $produitSelectionne->prix_vente)
                                            <div class="mb-2">
                                                <p class="text-sm text-gray-600">Nouveau prix de vente</p>
                                                <p class="font-medium text-info">
                                                    {{ number_format($nouveauPrixVente, 2) }} Fc
                                                </p>
                                            </div>
                                        @endif

                                        @if(!empty($nouveauPrixAchat) && $nouveauPrixAchat != $produitSelectionne->prix_achat)
                                            <div class="mb-2">
                                                <p class="text-sm text-gray-600">Nouveau prix d'achat</p>
                                                <p class="font-medium text-info">
                                                    {{ number_format($nouveauPrixAchat, 2) }} Fc
                                                </p>
                                            </div>
                                        @endif

                                        @if(!empty($nouvelleDateExpiration) && (!$produitSelectionne->date_expiration || $produitSelectionne->date_expiration->format('Y-m-d') != $nouvelleDateExpiration))
                                            <div class="mb-2">
                                                <p class="text-sm text-gray-600">Nouvelle date d'expiration</p>
                                                <p class="font-medium text-info">
                                                    {{ \Carbon\Carbon::parse($nouvelleDateExpiration)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        @endif

                                        <button 
                                            wire:click="sauvegarderModifications"
                                            class="btn btn-success w-full mt-4"
                                        >
                                            Confirmer les modifications
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-base-200 p-4 rounded-box border border-base-300 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                <p class="mt-2">Sélectionnez un produit</p>
                                <p class="text-sm mt-1">Cliquez sur "Sélectionner" à côté d'un produit pour gérer son stock</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>