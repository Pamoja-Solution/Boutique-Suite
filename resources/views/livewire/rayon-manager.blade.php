<div>
    @include('gerant.nav')
    @section("titre","Gestion des Rayons")

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl rounded-box p-6">
                <!-- En-tête avec onglets -->
                <div class="tabs tabs-boxed bg-base-200 mb-6">
                    <button 
                        class="tab {{ $activeTab === 'rayons' ? 'tab-active' : '' }}" 
                        wire:click="switchTab('rayons')"
                    >
                        Rayons
                    </button>
                    <button 
                        class="tab {{ $activeTab === 'sousRayons' ? 'tab-active' : '' }}" 
                        wire:click="switchTab('sousRayons')"
                    >
                        Sous-Rayons
                    </button>
                </div>
    
                <!-- Barre de recherche et bouton d'ajout -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold text-base-content">
                        {{ $activeTab === 'rayons' ? 'Gestion des Rayons' : 'Gestion des Sous-Rayons' }}
                    </h2>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <div class="form-control">
                            <label class="input input-bordered flex items-center gap-2">
                                <input type="text" wire:model.live="search" placeholder="Rechercher..." class="grow">
                                <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </label>
                        </div>
                        
                        <button wire:click.prevent="createRayon" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter Rayon
                        </button>
                        
                        <button wire:click.prevent="createSousRayon" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter Sous-Rayon
                        </button>
                    </div>
                </div>
    
                <!-- Messages de session -->
                @if (session()->has('message'))
                    <div role="alert" class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
    
                <!-- Contenu des onglets -->
                @if($activeTab === 'rayons')
                    <!-- Tableau des rayons -->
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="bg-base-200">
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Icône</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rayons as $rayon)
                                    <tr class="hover:bg-base-200" wire:key="product-card-{{ $rayon->id }}-{{ $loop->index }}">
                                        <td>{{ $rayon->code }}</td>
                                        <td>
                                            <div class="font-bold">{{ $rayon->nom }}</div>
                                            <div class="text-sm opacity-70">{{ Str::limit($rayon->description, 30) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-xl">
                                                <i class="fas fa-{{ $rayon->icon }}"></i>
                                            </div>
                                        </td>
                                        <td>{{ $rayon->ordre_affichage }}</td>
                                        <td>
                                            <div class="flex gap-2">
                                                <button wire:click="editRayon({{ $rayon->id }})" class="btn btn-sm  btn-ghost text-info" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteRayon({{ $rayon->id }})" class="btn btn-sm  btn-ghost text-error" title="Supprimer" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce rayon ?') || event.stopImmediatePropagation()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-base-content/70">
                                            Aucun rayon trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $rayons->links() }}
                    </div>
                @else
                    <!-- Tableau des sous-rayons -->
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="bg-base-200">
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Rayon parent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sousRayons as $sousRayon)
                                    <tr class="hover:bg-base-200">
                                        <td>{{ $sousRayon->code_emplacement }}</td>
                                        <td class="font-bold">{{ $sousRayon->nom }}</td>
                                        <td>
                                            @if($sousRayon->rayon)
                                                <div class="badge badge-outline">
                                                    {{ $sousRayon->rayon->nom }} ({{ $sousRayon->rayon->code }})
                                                </div>
                                            @else
                                                <span class="text-sm opacity-70">Non assigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex gap-2">
                                                <button wire:click="editSousRayon({{ $sousRayon->id }})" class="btn btn-sm  btn-ghost text-info" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteSousRayon({{ $sousRayon->id }})" class="btn btn-sm  btn-ghost text-error" title="Supprimer" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce sous-rayon ?') || event.stopImmediatePropagation()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-base-content/70">
                                            Aucun sous-rayon trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $sousRayons->links() }}
                    </div>
                @endif
            </div>
        </div>
    
        <!-- Modal Form -->
        @if($isOpen)
            <div class="modal modal-open">
                <div class="modal-box max-w-2xl">
                    @if($action === 'createRayon' || $action === 'editRayon')
                        <form wire:submit.prevent="{{ $action === 'createRayon' ? 'storeRayon' : 'storeRayon' }}">
                            <h3 class="font-bold text-lg mb-4">
                                {{ $action === 'createRayon' ? 'Créer un nouveau rayon' : 'Modifier le rayon' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Nom</span>
                                    </label>
                                    <input type="text" wire:model="rayon_nom" class="input input-bordered">
                                    @error('rayon_nom') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Code</span>
                                        </label>
                                        <input type="text" wire:model="rayon_code" class="input input-bordered">
                                        @error('rayon_code') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Icône</span>
                                        </label>
                                        <select wire:model="rayon_icon" class="select select-bordered">
                                            <option value="box">Box</option>
                                            <option value="wine-bottle">Bouteille</option>
                                            <option value="apple-alt">Fruits</option>
                                            <option value="bread-slice">Pain</option>
                                            <option value="cheese">Fromage</option>
                                            <option value="fish">Poisson</option>
                                            <option value="hamburger">Fast-food</option>
                                            <option value="ice-cream">Glace</option>
                                            <option value="lemon">Agrumes</option>
                                            <option value="wine-glass-alt">Boissons</option>
                                        </select>
                                        @error('rayon_icon') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Description</span>
                                    </label>
                                    <textarea wire:model="rayon_description" rows="3" class="textarea textarea-bordered"></textarea>
                                    @error('rayon_description') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Ordre d'affichage</span>
                                    </label>
                                    <input type="number" wire:model="rayon_ordre" class="input input-bordered">
                                    @error('rayon_ordre') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="modal-action">
                                <button type="button" wire:click="closeModal" class="btn">Annuler</button>
                                <button type="submit" class="btn btn-primary" >
                                    {{ $action === 'createRayon' ? 'Créer' : 'Mettre à jour' }}
                                </button>
                            </div>
                        </form>
                    @elseif($action === 'createSousRayon' || $action === 'editSousRayon')
                        <form wire:submit.prevent="{{ $action === 'createSousRayon' ? 'storeSousRayon' : 'storeSousRayon' }}">
                            <h3 class="font-bold text-lg mb-4">
                                {{ $action === 'createSousRayon' ? 'Créer un nouveau sous-rayon' : 'Modifier le sous-rayon' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Nom</span>
                                    </label>
                                    <input type="text" wire:model="sous_rayon_nom" class="input input-bordered">
                                    @error('sous_rayon_nom') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Code emplacement</span>
                                        </label>
                                        <input type="text" wire:model="sous_rayon_code" class="input input-bordered">
                                        @error('sous_rayon_code') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Rayon parent</span>
                                        </label>
                                        <select wire:model="sous_rayon_rayon_id" class="select select-bordered">
                                            <option value="">Sélectionner un rayon</option>
                                            @foreach($allRayons as $rayon)
                                                <option value="{{ $rayon->id }}">{{ $rayon->nom }}</option>
                                            @endforeach
                                        </select>
                                        @error('sous_rayon_rayon_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-action">
                                <button type="button" wire:click="closeModal" class="btn">Annuler</button>
                                <button type="submit" class="btn btn-primary">
                                    {{ $action === 'createSousRayon' ? 'Créer' : 'Mettre à jour' }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>