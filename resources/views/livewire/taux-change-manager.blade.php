<div class="">
    @include('gerant.nav')
    @section("titre","Gestion Taux")

<div class="container mx-auto px-4 py-6">

    <!-- En-tête avec bouton -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des Taux de Change</h1>
        <button 
            wire:click="create" 
            class="btn btn-primary"
        >
            <i class="fas fa-plus mr-2"></i> Nouveau Taux
        </button>
    </div>

    <!-- Barre de recherche -->
    <div class="mb-6">
        <label class="input input-bordered flex items-center gap-2">
            <input 
                type="text" 
                wire:model.debounce.300ms="searchTerm"
                placeholder="Rechercher par monnaie ou code..." 
                class="grow"
            >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
            </svg>
        </label>
    </div>

    <!-- Tableau des taux de change -->
    <div class="bg-base-100 rounded-box shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr class="bg-base-200">
                        <th>Source</th>
                        <th>Cible</th>
                        <th>Taux</th>
                        <th>Date Effet</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tauxChanges as $taux)
                        <tr class="hover:bg-base-200" wire:key="product-card-{{ $taux->id }}-{{ $loop->index }}">
                            <td>
                                <div class="font-bold">{{ $taux->monnaieSource->libelle }}</div>
                                <div class="text-sm opacity-70">{{ $taux->monnaieSource->code }}</div>
                            </td>
                            <td>
                                <div class="font-bold">{{ $taux->monnaieCible->libelle }}</div>
                                <div class="text-sm opacity-70">{{ $taux->monnaieCible->code }}</div>
                            </td>
                            <td>{{ number_format($taux->taux, 6) }}</td>
                            <td>{{ $taux->date_effet->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <button 
                                        wire:click="edit({{ $taux->id }})" 
                                        class="btn btn-primary btn-md text-info"
                                        title="Modifier"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $taux->id }})" 
                                        class="btn btn-error btn-md "
                                        title="Supprimer"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-base-content/70">
                                Aucun taux de change trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-base-200 border-t border-base-300">
            {{ $tauxChanges->links() }}
        </div>
    </div>

    <!-- Modal de création/édition -->
    @if($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg">
                    {{ $taux_id ? 'Modifier Taux de Change' : 'Créer un Nouveau Taux de Change' }}
                </h3>
                <form wire:submit.prevent="store">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Monnaie Source</span>
                            </label>
                            <select 
                                wire:model="monnaie_source_id" 
                                class="select select-bordered"
                            >
                                <option value="">Sélectionnez une monnaie</option>
                                @foreach($monnaies as $monnaie)
                                    <option value="{{ $monnaie->id }}">{{ $monnaie->libelle }} ({{ $monnaie->code }})</option>
                                @endforeach
                            </select>
                            @error('monnaie_source_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Monnaie Cible</span>
                            </label>
                            <select 
                                wire:model="monnaie_cible_id" 
                                class="select select-bordered"
                            >
                                <option value="">Sélectionnez une monnaie</option>
                                @foreach($monnaies as $monnaie)
                                    <option value="{{ $monnaie->id }}">{{ $monnaie->libelle }} ({{ $monnaie->code }})</option>
                                @endforeach
                            </select>
                            @error('monnaie_cible_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Taux</span>
                            </label>
                            <input 
                                type="number" 
                                wire:model="taux" 
                                step="0.000001" 
                                min="0.000001" 
                                class="input input-bordered"
                            >
                            @error('taux') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Date d'effet</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="date_effet" 
                                class="input input-bordered"
                            >
                            @error('date_effet') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn btn-error">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de confirmation de suppression -->
    @if($confirmingDelete)
        <div class="modal modal-open">
            <div class="modal-box">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Confirmer la suppression</h3>
                        <p class="py-4">Êtes-vous sûr de vouloir supprimer ce taux de change ? Cette action est irréversible.</p>
                    </div>
                </div>
                
                <div class="modal-action">
                    <button type="button" wire:click="cancelDelete" class="btn">Annuler</button>
                    <button type="button" wire:click="delete" class="btn btn-error">Supprimer</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Message flash -->
    @if (session()->has('message'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 3000)" 
            class="toast toast-end"
        >
            <div class="alert alert-success">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
</div>
</div>