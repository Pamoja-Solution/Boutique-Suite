<div>
    @include('gerant.nav')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-base-100 rounded-box shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des Dépenses</h2>
                <button 
                    wire:click="create" 
                    class="btn btn-primary"
                >
                    Nouvelle Dépense
                </button>
            </div>
    
            <!-- Search Bar -->
            <div class="mb-6">
                <label class="input input-bordered flex items-center gap-2">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Rechercher par motif..." 
                        class="grow"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                        <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                    </svg>
                </label>
            </div>
    
            <!-- Expenses Table -->
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Date</th>
                            <th>Motif</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-base-200" wire:key="product-card-{{ $expense->id }}-{{ $loop->index }}">
                                <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                                <td class="font-bold">{{ Str::limit($expense->motif,40) }}</td>
                                <td class="{{ $expense->type === 'income' ? 'text-success' : 'text-error' }}">
                                    {{ number_format($expense->amount, 2) }} FC
                                </td>
                                <td>
                                    <span class="badge {{ $expense->type === 'income' ? 'badge-success' : 'badge-error' }}">
                                        {{ $expense->type === 'income' ? 'Revenu' : 'Dépense' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button wire:click="edit({{ $expense->id }})" class="btn btn-ghost btn-sm text-info">Modifier</button>
                                        @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
                                            <button wire:click="confirmDelete({{ $expense->id }})" class="btn btn-ghost btn-sm text-error">Supprimer</button>
                                        @endif
                                        @if ($expense->attachment_path)
                                            <a class="btn btn-sm" href="storage/{{ $expense->attachment_path }}" target="_blank" rel="noopener noreferrer">
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                                </svg>
                                                Preuve
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-base-content/70">
                                    Aucune dépense trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            <!-- Pagination -->
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    
        <!-- Create/Edit Modal -->
        @if($isOpen)
            <div class="modal modal-open">
                <div class="modal-box max-w-2xl">
                    <h3 class="font-bold text-lg">
                        {{ $expenseId ? 'Modifier la dépense' : 'Ajouter une dépense' }}
                    </h3>
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Type</span>
                                </label>
                                <select wire:model="type" class="select select-bordered">
                                    <option value="expense">Dépense</option>
                                    <option value="income">Revenu</option>
                                </select>
                                @error('type') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Montant</span>
                                </label>
                                <input wire:model="amount" type="number" step="0.01" class="input input-bordered">
                                @error('amount') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
    
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Motif</span>
                            </label>
                            <textarea wire:model="motif" rows="4" class="textarea textarea-bordered" placeholder="Écris le motif de la transaction"></textarea>
                            @error('motif') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
    
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Justificatif</span>
                            </label>
                            <input wire:model="attachment" type="file" class="file-input file-input-bordered w-full">
                            @error('attachment') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
    
                        <div class="modal-action">
                            <button type="button" wire:click="closeModal" class="btn">Annuler</button>
                            <button type="button" wire:click="store" class="btn btn-primary">
                                {{ $expenseId ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    
        <!-- Delete Confirmation Modal -->
        @if($confirmingDeletion)
            <div class="modal modal-open">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">Confirmer la suppression</h3>
                    <p class="py-4">Êtes-vous sûr de vouloir supprimer cette dépense? Cette action est irréversible.</p>
                    <div class="modal-action">
                        <button type="button" wire:click="$set('confirmingDeletion', false)" class="btn">Annuler</button>
                        <button type="button" wire:click="delete({{ $confirmingDeletion }})" class="btn btn-error">Supprimer</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>