<div class="">
    @include('gerant.nav')
    @section("titre","Code-Barre")

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-base-content mb-2">Gestion des Codes-Barres</h1>
        <p class="text-base-content/70">Gérez et imprimez les codes-barres de vos produits</p>
    </div>

    {{-- Barre de recherche et actions --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="form-control flex-1">
                    <input 
                        type="text" 
                        placeholder="Rechercher par nom, code-barres ou référence..." 
                        class="input input-bordered w-full"
                        wire:model.live.debounce.300ms="search"
                    />
                </div>
                
                <div class="flex gap-2">
                    <button class="btn btn-outline" wire:click="selectAll">
                        Tout sélectionner
                    </button>
                    <button class="btn btn-outline" wire:click="deselectAll">
                        Tout désélectionner
                    </button>
                    @if(!empty($selectedProduits))
                        <button class="btn btn-primary" wire:click="printSelected">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimer PDF ({{ count($selectedProduits) }})
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Table des produits --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>
                                    <input type="checkbox" class="checkbox checkbox-neutral" 
                                           @if(count($selectedProduits) === $produits->count() && $produits->count() > 0) checked @endif
                                           wire:click="@if(count($selectedProduits) === $produits->count()) deselectAll @else selectAll @endif"
                                    />
                            </th>
                            <th>Produit</th>
                            <th>Code-Barres</th>
                            <th>Référence</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produits as $produit)
                            <tr>
                                <td>
                                    <label>
                                        <input type="checkbox" class="checkbox checkbox-neutral" 
                                               value="{{ $produit->id }}"
                                               wire:model.live="selectedProduits"
                                        />
                                    </label>
                                </td>
                                <td>
                                    <div class="font-bold">{{ $produit->nom }}</div>
                                    <div class="text-sm opacity-50">{{ $produit->unite_mesure }}</div>
                                </td>
                                <td>
                                    @if($produit->code_barre)
                                        <div class="flex flex-col items-center">
                                            <div class="mb-2">
                                                {!! $produit->barcode_svg ?? '' !!}
                                            </div>
                                            <div class="font-mono text-xs">{{ $produit->code_barre }}</div>
                                        </div>
                                    @else
                                        <span class="badge badge-warning">Non généré</span>
                                    @endif
                                </td>
                                <td>{{ $produit->reference_interne }}</td>
                                <td>{{ number_format($produit->prix_vente, 2) }} FC</td>
                                <td>
                                    <span class="badge @if($produit->stock <= $produit->seuil_alerte) badge-error @else badge-success @endif">
                                        {{ $produit->stock }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </div>
                                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            @if(!$produit->code_barre)
                                                <li><a wire:click="generateCodeBarre({{ $produit->id }})">Générer code-barres</a></li>
                                            @endif
                                            <li><a wire:click="editCodeBarre({{ $produit->id }})">Modifier code-barres</a></li>
                                            @if($produit->code_barre)
                                                <li><a wire:click="printSingle({{ $produit->id }})">Imprimer PDF</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="text-base-content/50">Aucun produit trouvé</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="mt-6">
                {{ $produits->links() }}
            </div>
        </div>
    </div>

    {{-- Modal d'édition --}}
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Modifier le code-barres</h3>
                <p class="py-4">Produit: <strong>{{ $editingProduit->nom ?? '' }}</strong></p>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Code-barres</span>
                    </label>
                    <input type="text" 
                           class="input input-bordered @error('editingCodeBarre') input-error @enderror" 
                           wire:model="editingCodeBarre"
                           placeholder="Entrez le code-barres"
                    />
                    @error('editingCodeBarre') 
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="modal-action">
                    <button class="btn btn-ghost" wire:click="closeModal">Annuler</button>
                    <button class="btn btn-primary" wire:click="updateCodeBarre">Sauvegarder</button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('code-barre-generated', () => {
        // Optionnel: afficher une notification
        console.log('Code-barres généré avec succès');
    });
    
    Livewire.on('code-barre-updated', () => {
        // Optionnel: afficher une notification
        console.log('Code-barres mis à jour avec succès');
    });
});
</script>