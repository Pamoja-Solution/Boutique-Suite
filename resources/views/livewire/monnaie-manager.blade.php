<div class="py-6">
    @include('gerant.nav')
    @section("titre","Gestion Devise")
    
    <div class="py-6 mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-100 overflow-hidden shadow-sm rounded-box">
            <div class="p-6">
                
                <!-- En-tête -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Gestion des Monnaies</h2>
                    <div class="flex gap-4">
                        <div>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Rechercher..."
                                   class="input input-bordered">
                        </div>
                        <button wire:click="create" 
                                class="btn btn-primary">
                            Ajouter une monnaie
                        </button>
                    </div>
                </div>
                
                <!-- Message de confirmation -->
                @if (session()->has('message'))
                    <div class="alert alert-success mb-4">
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
                
                <!-- Tableau des monnaies -->
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Symbole</th>
                                <th>Code</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monnaies as $monnaie)
                                <tr class="hover" wire:key="product-card-{{ $monnaie->id }}-{{ $loop->index }}">
                                    <td>{{ $monnaie->libelle }}</td>
                                    <td>{{ $monnaie->symbole }}</td>
                                    <td>{{ $monnaie->code }}</td>
                                    <td>
                                        <span class="badge {{ $monnaie->statut == '1' ? 'badge-success' : 'badge-error' }}">
                                            {{ $monnaie->statut == '1' ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $monnaie->id }})" 
                                                class="btn btn-ghost btn-sm text-primary">
                                            Modifier
                                        </button>
                                        <button wire:click="confirmDelete({{ $monnaie->id }})" 
                                                class="btn btn-ghost btn-sm text-error">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500">
                                        Aucune monnaie trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $monnaies->links() }}
                </div>
                
                <!-- Modal de formulaire -->
                @if($isOpen)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">
                                {{ $monnaie_id ? 'Modifier la monnaie' : 'Ajouter une monnaie' }}
                            </h3>
                            <div class="mt-4">
                                <form>
                                    <div class="form-control mb-4">
                                        <label class="label">
                                            <span class="label-text">Libellé</span>
                                        </label>
                                        <input type="text" id="libelle" wire:model="libelle" 
                                               class="input input-bordered">
                                        @error('libelle') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="form-control mb-4">
                                        <label class="label">
                                            <span class="label-text">Symbole</span>
                                        </label>
                                        <input type="text" id="symbole" wire:model="symbole" 
                                               class="input input-bordered">
                                        @error('symbole') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="form-control mb-4">
                                        <label class="label">
                                            <span class="label-text">Code (ISO)</span>
                                        </label>
                                        <input type="text" id="code" wire:model="code" 
                                               class="input input-bordered">
                                        @error('code') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="form-control mb-4">
                                        <label class="label">
                                            <span class="label-text">Statut</span>
                                        </label>
                                        <select id="statut" wire:model="statut" 
                                                class="select select-bordered">
                                            <option value="0">Inactif</option>
                                            <option value="1">Actif</option>
                                        </select>
                                        @error('statut') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </form>
                            </div>
                            <div class="modal-action">
                                <button wire:click="store" type="button" 
                                        class="btn btn-primary">
                                    {{ $monnaie_id ? 'Mettre à jour' : 'Enregistrer' }}
                                </button>
                                <button wire:click="closeModal" type="button" 
                                        class="btn">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Modal de confirmation de suppression -->
                @if($confirmingDelete)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">
                                        Confirmation de suppression
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm">
                                            Êtes-vous sûr de vouloir supprimer cette monnaie ? Cette action ne peut pas être annulée.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-action">
                                <button wire:click="delete" type="button" 
                                        class="btn btn-error">
                                    Supprimer
                                </button>
                                <button wire:click="cancelDelete" type="button" 
                                        class="btn">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>