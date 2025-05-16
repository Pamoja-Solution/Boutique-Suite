<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-100 overflow-hidden shadow-xl rounded-box p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Gestion des Produits</h2>
                <div class="flex items-center gap-4">
                    <div class="form-control">
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live="search" 
                                placeholder="Rechercher..." 
                                class="input input-bordered pr-10"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button 
                        wire:click="create" 
                        class="btn btn-primary"
                    >
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter
                    </button>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success mb-4">
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Prix Vente</th>
                            <th>Stock</th>
                            <th>Emplacement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produits as $produit)
                            <tr class="hover" wire:key="product-card-{{ $produit->id }}-{{ $loop->index }}">
                                <td>
                                    {{ $produit->reference_interne }}
                                    @if($produit->code_barre)
                                        <div class="text-xs text-gray-500">{{ $produit->code_barre }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-medium">{{ $produit->nom }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($produit->description, 30) }}</div>
                                </td>
                                <td>
                                    {{ number_format($produit->prix_vente, 2) }} Fc
                                    <div class="text-xs text-gray-500">{{ number_format($produit->prix_achat, 2) }} Fc (achat)</div>
                                </td>
                                <td>
                                    <span class="badge 
                                        {{ $produit->stock > $produit->seuil_alerte ? 'badge-success' : 
                                          ($produit->stock > 0 ? 'badge-warning' : 'badge-error') }}">
                                        {{ $produit->stock }} {{ $produit->unite_mesure }}
                                    </span>
                                    @if($produit->stock <= $produit->seuil_alerte)
                                        <div class="text-xs text-error">Seuil: {{ $produit->seuil_alerte }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($produit->sousRayon)
                                        {{ $produit->sousRayon->rayon->nom }} / {{ $produit->sousRayon->nom }}
                                        <div class="text-xs text-gray-500">{{ $produit->sousRayon->code_emplacement }}</div>
                                    @else
                                        Non assigné
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button 
                                            wire:click="addStock({{ $produit->id }})" 
                                            class="btn btn-ghost btn-sm text-success"
                                            title="Ajouter du stock"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="edit({{ $produit->id }})" 
                                            class="btn btn-ghost btn-sm text-primary"
                                            title="Modifier"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="delete({{ $produit->id }})" 
                                            class="btn btn-ghost btn-sm text-error"
                                            title="Supprimer" 
                                            onclick="confirm('Êtes-vous sûr de vouloir supprimer ce produit ?') || event.stopImmediatePropagation()"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500">
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
    </div>

    <!-- Modal Form -->
    @if($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-3xl">
                @if($action == 'addStock')
                    <form wire:submit.prevent="saveStock">
                        <h3 class="font-bold text-lg mb-4">Ajouter du stock</h3>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Quantité à ajouter au stock</span>
                            </label>
                            <input 
                                type="number" 
                                id="stock_a_ajouter" 
                                wire:model.defer="stock_a_ajouter" 
                                class="input input-bordered"
                                min="1"
                            >
                            @error('stock_a_ajouter') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-action">
                            <button type="submit" class="btn btn-success">
                                Ajouter au stock
                            </button>
                            <button type="button" wire:click="closeModal" class="btn">
                                Annuler
                            </button>
                        </div>
                    </form>
                @else
                    <form wire:submit.prevent="{{ $action == 'create' ? 'store' : 'update' }}">
                        <h3 class="font-bold text-lg mb-4">{{ $action == 'create' ? 'Ajouter un produit' : 'Modifier le produit' }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="nom" 
                                    wire:model.defer="nom" 
                                    class="input input-bordered"
                                >
                                @error('nom') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Description</span>
                                </label>
                                <textarea 
                                    id="description" 
                                    wire:model.defer="description" 
                                    rows="3" 
                                    class="textarea textarea-bordered"
                                ></textarea>
                                @error('description') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Prix de vente (Fc)</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="prix_vente" 
                                    wire:model.defer="prix_vente" 
                                    step="0.01" 
                                    class="input input-bordered"
                                >
                                @error('prix_vente') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Prix d'achat (Fc)</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="prix_achat" 
                                    wire:model.defer="prix_achat" 
                                    step="0.01" 
                                    class="input input-bordered"
                                >
                                @error('prix_achat') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Stock</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="stock" 
                                    wire:model.defer="stock" 
                                    class="input input-bordered"
                                >
                                @error('stock') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Seuil d'alerte</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="seuil_alerte" 
                                    wire:model.defer="seuil_alerte" 
                                    class="input input-bordered"
                                >
                                @error('seuil_alerte') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Unité de mesure</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="unite_mesure" 
                                    wire:model.defer="unite_mesure" 
                                    class="input input-bordered"
                                >
                                @error('unite_mesure') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Taxable</span>
                                    <input 
                                        type="checkbox" 
                                        id="taxable" 
                                        wire:model.defer="taxable" 
                                        class="checkbox"
                                    >
                                </label>
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fournisseur</span>
                                </label>
                                <select 
                                    id="fournisseur_id" 
                                    wire:model.defer="fournisseur_id" 
                                    class="select select-bordered"
                                >
                                    <option value="">Sélectionner un fournisseur</option>
                                    @foreach($fournisseurs as $fournisseur)
                                        <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Rayon</span>
                                </label>
                                <select 
                                    id="rayon_id" 
                                    wire:model="rayon_id" 
                                    wire:change="$refresh" 
                                    class="select select-bordered"
                                >
                                    <option value="">Sélectionner un rayon</option>
                                    @foreach($rayons as $rayon)
                                        <option value="{{ $rayon->id }}">{{ $rayon->nom }}</option>
                                    @endforeach
                                </select>
                                @error('rayon_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Sous-rayon</span>
                                </label>
                                <select 
                                    id="sous_rayon_id" 
                                    wire:model="sous_rayon_id" 
                                    class="select select-bordered"
                                    {{ !$rayon_id ? 'disabled' : '' }}
                                >
                                    <option value="">Sélectionner un sous-rayon</option>
                                    @if($rayon_id)
                                        @foreach($sousRayons as $sousRayon)
                                            <option value="{{ $sousRayon->id }}">{{ $sousRayon->nom }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('sous_rayon_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Référence interne</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="reference_interne" 
                                    wire:model.defer="reference_interne" 
                                    class="input input-bordered"
                                >
                                @error('reference_interne') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Code-barres</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="code_barre" 
                                    wire:model.defer="code_barre" 
                                    class="input input-bordered"
                                >
                                @error('code_barre') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="modal-action">
                            <button type="submit" class="btn btn-primary">
                                {{ $action == 'create' ? 'Créer' : 'Mettre à jour' }}
                            </button>
                            <button type="button" wire:click="closeModal" class="btn">
                                Annuler
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>