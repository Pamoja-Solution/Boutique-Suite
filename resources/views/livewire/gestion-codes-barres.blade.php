<div class="container mx-auto p-4">
    
    <h1 class="text-2xl font-bold mb-6">Gestion des Codes Barres</h1>

    <!-- Barre de recherche -->
    <div class="mb-6">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Rechercher par nom, code barre ou référence..." 
            class="input input-bordered w-full"
        >
    </div>

    <!-- Options d'impression -->
    <div class="bg-base-200 p-4 rounded-lg mb-6">
        <h2 class="text-lg font-semibold mb-3">Options d'impression</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">
                    <span class="label-text">Nombre par ligne</span>
                </label>
                <select wire:model="nombreParLigne" class="select select-bordered w-full">
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4" selected>4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div>
                <label class="label">
                    <span class="label-text">Taille du code barre</span>
                </label>
                <select wire:model="tailleCodeBarre" class="select select-bordered w-full">
                    <option value="small">Petit</option>
                    <option value="medium" selected>Moyen</option>
                    <option value="large">Grand</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex justify-between mb-4">
        <div>
            @if(count($selectedProduits) > 0)
                <button wire:click="imprimerSelection" class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                    Imprimer la sélection ({{ count($selectedProduits) }})
                </button>
            @endif
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="bg-base-100 rounded-lg shadow overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" wire:model="selectAll" />
                        </label>
                    </th>
                    <th>Produit</th>
                    <th>Référence</th>
                    <th>Code Barre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $produit)
                <tr>
                    <td>
                        <input 
                            type="checkbox" 
                            class="checkbox" 
                            wire:model="selectedProduits" 
                            value="{{ $produit->id }}"
                        />
                    </td>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->reference_interne }}</td>
                    <td>
                        @if($produit->code_barre)
                            {{ $produit->code_barre }}
                            <div class="mt-1">
                                {!! DNS1D::getBarcodeSVG($produit->code_barre, 'C128', 1, 30) !!}                            </div>
                        @else
                            <span class="text-warning">Non défini</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex space-x-2">
                            <button 
                                wire:click="imprimerCodeBarre({{ $produit->id }})" 
                                class="btn btn-sm btn-ghost"
                                title="Imprimer"
                            >
                            <i class="fa-solid fa-print"></i>

                            </button>
                            <button 
                                wire:click="editCodeBarre({{ $produit->id }})" 
                                class="btn btn-sm btn-ghost"
                                title="Modifier"
                            >
                            <i class="fa-solid fa-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $produits->links() }}
    </div>

    <!-- Modal d'édition -->
    <dialog id="editModal" class="modal" @if($showEditModal) open @endif>
        <div class="modal-box">
            <h3 class="font-bold text-lg">Modifier le code barre</h3>
            <div class="py-4">
                <div class="mb-4">
                    <label class="label">
                        <span class="label-text">Produit</span>
                    </label>
                    <input type="text" class="input input-bordered w-full" value="{{ $produitEdit->nom ?? '' }}" disabled>
                </div>
                <div class="mb-4">
                    <label class="label">
                        <span class="label-text">Code Barre</span>
                    </label>
                    <input 
                        type="text" 
                        class="input input-bordered w-full" 
                        wire:model="newCodeBarre"
                        placeholder="Entrez le nouveau code barre"
                    >
                    @error('newCodeBarre')
                        <div class="text-error mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @if($produitEdit && $produitEdit->code_barre)
                <div class="mt-2 text-center">
                    {!! DNS1D::getBarcodeSVG($produitEdit->code_barre, 'EAN13', 1, 30) !!}
                </div>
                @endif
            </div>
            <div class="modal-action">
                <button wire:click="updateCodeBarre" class="btn btn-primary">Enregistrer</button>
                <button wire:click="$set('showEditModal', false)" class="btn">Annuler</button>
            </div>
        </div>
    </dialog>
</div>