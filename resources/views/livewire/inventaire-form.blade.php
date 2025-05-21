<div>
    <form wire:submit="save" class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">{{ $isEdit ? 'Modifier' : 'Créer' }} un inventaire</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Référence</span>
                    </label>
                    <input type="text" wire:model="reference" class="input input-bordered" {{ $isEdit ? 'readonly' : '' }} />
                    @error('reference') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Statut</span>
                    </label>
                    <select wire:model="statut" class="select select-bordered" {{ $isTermine ? 'disabled' : '' }}>
                        <option value="brouillon">Brouillon</option>
                        <option value="en_cours">En cours</option>
                        <option value="terminé">Terminé</option>
                        <option value="annulé">Annulé</option>
                    </select>
                    @error('statut') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Motif</span>
                    </label>
                    <input type="text" wire:model="motif" class="input input-bordered" {{ $isTermine ? 'readonly' : '' }} />
                    @error('motif') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Notes</span>
                    </label>
                    <textarea wire:model="notes" class="textarea textarea-bordered" rows="3" {{ $isTermine ? 'readonly' : '' }}></textarea>
                    @error('notes') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="card-actions justify-end mt-4">
                <a href="{{ route('inventaires.index') }}" class="btn btn-ghost">Annuler</a>
                @if(!$isTermine)
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                @endif
            </div>
        </div>
    </form>
</div>