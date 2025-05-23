<x-app-layout>
    @include('gerant.nav')
    @section("titre",$inventaire->reference)

    <div class="px-2 sm:px-3 lg:px-4 py-3">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Détails de l\'inventaire') }}
        </h2>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                <div class="card bg-base-100 shadow-xl mb-6">
                    <div class="card-body">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                            <div>
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
                                <p class="text-sm">Créé le {{ $inventaire->created_at->format('d/m/Y H:i') }} par {{ $inventaire->user->name }}</p>
                                <p class="mt-2">{{ $inventaire->motif }}</p>
                                @if($inventaire->notes)
                                <p class="text-sm mt-1">{{ $inventaire->notes }}</p>
                                @endif
                            </div>
                            
                            <div class="flex gap-2 mt-4 md:mt-0">
                                <a href="{{ route('inventaires.index') }}" class="btn btn-ghost">
                                    <i class="fas fa-arrow-left mr-2"></i> Retour
                                </a>
                                @if($inventaire->statut !== 'terminé')
                                <a href="{{ route('inventaires.edit', $inventaire->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit mr-2"></i> Modifier
                                </a>
                                @if($inventaire->statut === 'brouillon' || $inventaire->statut === 'en_cours')
                                <a href="{{ route('inventaires.mouvements', $inventaire->id) }}" class="btn btn-primary">
                                    <i class="fas fa-list mr-2"></i> Mouvements
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="text-lg font-bold mb-4">Détails de l'inventaire</h3>
                        
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mouvements as $mouvement)
                                    <tr>
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
                                        <td class="text-center">{{ $mouvement->quantite_reelle ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($mouvement->quantite_reelle !== null)
                                                <span class="font-bold {{ $mouvement->ecart < 0 ? 'text-error' : ($mouvement->ecart > 0 ? 'text-success' : '') }}">
                                                    {{ $mouvement->ecart > 0 ? '+' : '' }}{{ $mouvement->ecart }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Aucun mouvement trouvé pour cet inventaire</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($inventaire->statut === 'terminé')
                        <div class="mt-6">
                            <h3 class="text-lg font-bold mb-4">Résumé</h3>
                            <div class="stats shadow w-full">
                                <div class="stat">
                                    <div class="stat-title">Produits inventoriés</div>
                                    <div class="stat-value">{{ $totalProduits }}</div>
                                </div>
                                
                                <div class="stat">
                                    <div class="stat-title">Écarts positifs</div>
                                    <div class="stat-value text-success">{{ $ecartPositifs }}</div>
                                </div>
                                
                                <div class="stat">
                                    <div class="stat-title">Écarts négatifs</div>
                                    <div class="stat-value text-error">{{ $ecartNegatifs }}</div>
                                </div>
                                
                                <div class="stat">
                                    <div class="stat-title">Sans écart</div>
                                    <div class="stat-value">{{ $sansEcart }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>