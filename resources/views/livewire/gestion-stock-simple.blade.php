@section("titre",'Gestion des stocks')
<div class="">
        @include('gerant.nav')
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">Gestion des stocks</h1>
                    
                    <!-- Barre de recherche -->
                    <div class="mb-6">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Rechercher un produit..."
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                    </div>
        
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Liste des produits -->
                        <div class="md:col-span-2">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Référence</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Stock</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Prix</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                        @forelse($produits as $produit)
                                            <tr @class([
                                                'bg-red-50 dark:bg-red-900/20' => $produit->stock <= $produit->seuil_alerte,
                                                'hover:bg-gray-50 dark:hover:bg-gray-700' => true
                                            ])>
                                                <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                                                    <div class="font-medium">{{ $produit->nom }}</div>
                                                    @if($produit->date_expiration)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            Exp: {{ $produit->date_expiration->format('d/m/Y') }}
                                                            @if($produit->date_expiration < now()->addDays(30))
                                                                <span class="text-red-500 dark:text-red-400">(Bientôt expiré)</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $produit->reference_interne }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span @class([
                                                        'px-2 py-1 text-xs font-semibold rounded-full',
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $produit->stock > $produit->seuil_alerte,
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $produit->stock <= $produit->seuil_alerte
                                                    ])>
                                                        {{ $produit->stock }} {{ $produit->unite_mesure }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ number_format($produit->prix_vente, 2) }} Fc
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button 
                                                        wire:click="selectionnerProduit({{ $produit->id }})"
                                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    >
                                                        Sélectionner
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
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
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Gestion du stock</h2>
                                    
                                    <div class="mb-4">
                                        <h3 class="font-medium dark:text-white">{{ $produitSelectionne->nom }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Ref: {{ $produitSelectionne->reference_interne }}</p>
                                        <p class="text-lg font-bold mt-2 dark:text-white">
                                            Stock actuel: 
                                            <span @class([
                                                'text-green-600 dark:text-green-400' => $produitSelectionne->stock > $produitSelectionne->seuil_alerte,
                                                'text-red-600 dark:text-red-400' => $produitSelectionne->stock <= $produitSelectionne->seuil_alerte
                                            ])>
                                                {{ $produitSelectionne->stock }} {{ $produitSelectionne->unite_mesure }}
                                            </span>
                                        </p>
                                    </div>
        
                                    <form wire:submit.prevent="calculerStock">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantité à ajouter/retirer</label>
                                                <input 
                                                    wire:model="quantiteAjout" 
                                                    type="number" 
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                                                    required
                                                >
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Entrez une valeur négative pour retirer du stock
                                                </p>
                                            </div>
        
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau prix de vente (optionnel)</label>
                                                <input 
                                                    wire:model="nouveauPrixVente" 
                                                    type="number" 
                                                    min="0" 
                                                    step="0.01"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                                                    placeholder="Laisser vide pour garder {{ number_format($produitSelectionne->prix_vente, 2) }} Fc"
                                                    value=""
                                                >
                                            </div>
                        
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau prix d'achat (optionnel)</label>
                                                <input 
                                                    wire:model="nouveauPrixAchat" 
                                                    type="number" 
                                                    min="0" 
                                                    step="0.01"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                                                    placeholder="Laisser vide pour garder {{ number_format($produitSelectionne->prix_achat, 2) }} Fc"
                                                    value=""
                                                >
                                            </div>
        
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date d'expiration (optionnel)</label>
                                                <input 
                                                    wire:model="nouvelleDateExpiration" 
                                                    type="date" 
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:[color-scheme:dark]"
                                                    value="{{ $nouvelleDateExpiration ?? '' }}"
                                                >
                                                @if($produitSelectionne->date_expiration)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        Actuelle: {{ is_string($produitSelectionne->date_expiration) 
                                                            ? \Carbon\Carbon::parse($produitSelectionne->date_expiration)->format('d/m/Y')
                                                            : $produitSelectionne->date_expiration->format('d/m/Y') }}
                                                    </p>
                                                @endif
                                            </div>
        
                                            <button 
                                                type="submit"
                                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600"
                                            >
                                                Calculer le nouveau stock
                                            </button>
                                        </div>
                                    </form>
        
                                    @if($nouveauStockCalcule !== null)
                                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <h3 class="font-medium text-lg mb-2 dark:text-white">Récapitulatif des modifications</h3>
                                            
                                            <div class="grid grid-cols-2 gap-4 mb-2">
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Stock actuel</p>
                                                    <p class="font-medium dark:text-white">{{ $stockActuel }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nouveau stock</p>
                                                    <p class="font-medium text-blue-600 dark:text-blue-400">
                                                        {{ $nouveauStockCalcule }}
                                                    </p>
                                                </div>
                                            </div>
        
                                            @if(!empty($nouveauPrixVente) && $nouveauPrixVente != $produitSelectionne->prix_vente)
                                                <div class="mb-2">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nouveau prix de vente</p>
                                                    <p class="font-medium text-blue-600 dark:text-blue-400">
                                                        {{ number_format($nouveauPrixVente, 2) }} Fc
                                                    </p>
                                                </div>
                                            @endif
        
                                            @if(!empty($nouveauPrixAchat) && $nouveauPrixAchat != $produitSelectionne->prix_achat)
                                                <div class="mb-2">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nouveau prix d'achat</p>
                                                    <p class="font-medium text-blue-600 dark:text-blue-400">
                                                        {{ number_format($nouveauPrixAchat, 2) }} Fc
                                                    </p>
                                                </div>
                                            @endif
        
                                            @if(!empty($nouvelleDateExpiration) && (!$produitSelectionne->date_expiration || $produitSelectionne->date_expiration->format('Y-m-d') != $nouvelleDateExpiration))
                                                <div class="mb-2">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nouvelle date d'expiration</p>
                                                    <p class="font-medium text-blue-600 dark:text-blue-400">
                                                        {{ \Carbon\Carbon::parse($nouvelleDateExpiration)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            @endif
        
                                            <button 
                                                wire:click="sauvegarderModifications"
                                                class="w-full mt-4 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600"
                                            >
                                                Confirmer les modifications
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 text-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
