<div>
    <!-- En-tête de l'inventaire -->
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h2 class="card-title">
                Inventaire {{ $inventaire->reference }} 
                <span class="badge ml-2 {{ 
                    $inventaire->statut === 'brouillon' ? 'badge-ghost' : 
                    ($inventaire->statut === 'en_cours' ? 'badge-warning' : 
                    ($inventaire->statut === 'terminé' ? 'badge-success' : 'badge-error')) 
                }}">
                    {{ ucfirst($inventaire->statut) }}
                </span>
            </h2>
            <p>{{ $inventaire->motif }}</p>
            @if($inventaire->notes)
            <p class="text-sm">{{ $inventaire->notes }}</p>
            @endif
        </div>
    </div>
    
    <!-- Section de recherche et actions -->
    <div class="bg-base-100 shadow-xl p-4 mb-6 rounded-box">
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
            <!-- Formulaire de recherche -->
            <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                <div class="form-control flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Rechercher un produit..." 
                           class="input input-bordered w-full" />
                </div>
                
                <div class="form-control flex-1">
                    <select wire:model.live="selectedSousRayon" class="select select-bordered w-full">
                        <option value="">Tous les emplacements</option>
                        @foreach($sousRayons as $sousRayon)
                        <option value="{{ $sousRayon->id }}">{{ $sousRayon->rayon->nom }} - {{ $sousRayon->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Statistiques et actions -->
            <div class="flex flex-col sm:flex-row gap-4 items-center w-full lg:w-auto">
                <div class="stats shadow bg-base-200">
                    <div class="stat">
                        <div class="stat-title">Produits inventoriés</div>
                        <div class="stat-value text-primary">{{ $mouvementsListe->total() }}</div>
                    </div>
                </div>
                
                @if($peutFinaliser)
                <button wire:click="finaliserInventaire" 
                        wire:confirm="Êtes-vous sûr de vouloir finaliser cet inventaire ?"
                        class="btn btn-success">
                    <i class="fas fa-check-circle mr-2"></i> Finaliser
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Résultats de recherche -->
   <!-- Résultats de recherche -->
@if($search)
<div class="bg-base-100 shadow-xl p-4 mb-6 rounded-box">
    <h3 class="text-lg font-bold mb-4">Résultats de recherche</h3>
    
    @if($produitsResults->count() > 0)
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Produit</th>
                    <th>Emplacement</th>
                    <th>Stock actuel</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produitsResults as $produit)
                <tr>
                    <td>{{ $produit->reference_interne }}</td>
                    <td>{{ $produit->nom }}</td>
                    <td>
                        @if($produit->sousRayon)
                            {{ $produit->sousRayon->rayon->nom }} - {{ $produit->sousRayon->nom }}
                        @else
                            <span class="text-error">Non défini</span>
                        @endif
                    </td>
                    <td>{{ $produit->stock }}</td>
                    <td>
                        <button wire:click="ajouterProduit({{ $produit->id }})" 
                                class="btn btn-xs btn-primary">
                            Ajouter à l'inventaire
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="alert alert-info">
        <div class="flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current"> 
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path> 
            </svg>
            <label>Aucun produit trouvé</label>
        </div>
    </div>
    @endif
</div>
@endif
    
    <!-- Liste des produits de l'inventaire -->
    <div class="bg-base-100 shadow-xl p-4 rounded-box">
        <h3 class="text-lg font-bold mb-4">Produits à inventorier</h3>
        
        @if($mouvementsListe->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Produit</th>
                        <th>Emplacement</th>
                        <th class="text-center">Stock théorique</th>
                        <th class="text-center">Stock réel</th>
                        <th class="text-center">Écart</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mouvementsListe as $mouvement)
                    <tr wire:key="mouvement-{{ $mouvement->id }}">
                        <td>{{ $mouvement->produit->reference_interne }}</td>
                        <td>{{ $mouvement->produit->nom }}</td>
                        <td>
                            @if($mouvement->produit->sousRayon)
                                {{ $mouvement->produit->sousRayon->rayon->nom }} - {{ $mouvement->produit->sousRayon->nom }}
                            @else
                                <span class="text-error">Non défini</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $mouvement->quantite_theorique }}</td>
                        <td class="text-center">
                            <input type="number" 
                                   wire:change="updateQuantiteReelle({{ $mouvement->id }}, $event.target.value)"
                                   value="{{ $mouvement->quantite_reelle ?? '' }}" 
                                   class="input input-bordered input-sm w-24 text-center" />
                        </td>
                        <td class="text-center">
                            @if($mouvement->quantite_reelle !== null)
                                @php $ecart = $mouvement->quantite_reelle - $mouvement->quantite_theorique; @endphp
                                <span class="font-bold {{ $ecart < 0 ? 'text-error' : ($ecart > 0 ? 'text-success' : '') }}">
                                    {{ $ecart > 0 ? '+' : '' }}{{ $ecart }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <button wire:click="retirerProduit({{ $mouvement->id }})" 
                                    class="btn btn-xs btn-error">
                                Retirer
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $mouvementsListe->links() }}
        </div>
        @else
        <div class="alert alert-warning">
            <div class="flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current"> 
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path> 
                </svg>
                <label>Aucun produit dans cet inventaire. Utilisez la recherche pour ajouter des produits.</label>
            </div>
        </div>
        @endif
    </div>
</div>