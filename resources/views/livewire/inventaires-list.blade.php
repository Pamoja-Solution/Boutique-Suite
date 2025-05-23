<div>
    <div class="flex flex-col lg:flex-row justify-between mb-4 gap-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="form-control">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher..." class="input input-bordered" />
            </div>
            
            <div class="form-control">
                <select wire:model.live="statutFiltre" class="select select-bordered">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon">Brouillon</option>
                    <option value="en_cours">En cours</option>
                    <option value="terminé">Terminé</option>
                    <option value="annulé">Annulé</option>
                </select>
            </div>
            
            <div class="form-control">
                <select wire:model.live="perPage" class="select select-bordered">
                    <option value="10">10 par page</option>
                    <option value="25">25 par page</option>
                    <option value="50">50 par page</option>
                    <option value="100">100 par page</option>
                </select>
            </div>
        </div>
        
        <div>
            <a href="{{ route('inventaires.create') }}" class="btn btn-primary" wire:navigate>
                <i class="fas fa-plus mr-2"></i> Nouvel inventaire
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Créé par</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventaires as $inventaire)
                <tr>
                    <td>{{ $inventaire->reference }}</td>
                    <td>{{ $inventaire->motif }}</td>
                    <td>
                        @switch($inventaire->statut)
                            @case('brouillon')
                                <span class="badge badge-ghost">Brouillon</span>
                                @break
                            @case('en_cours')
                                <span class="badge badge-warning">En cours</span>
                                @break
                            @case('terminé')
                                <span class="badge badge-success">Terminé</span>
                                @break
                            @case('annulé')
                                <span class="badge badge-error">Annulé</span>
                                @break
                        @endswitch
                    </td>
                    <td>{{ $inventaire->user->name }}</td>
                    <td>{{ $inventaire->created_at->format('d/m/Y H:i') }}</td>
                    <td class="flex gap-2">
                        <a href="{{ route('inventaires.show', $inventaire->id) }}" class="btn btn-sm btn-info" wire:navigate>
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($inventaire->statut !== 'terminé')
                        <a href="{{ route('inventaires.edit', $inventaire->id) }}" class="btn btn-sm btn-warning" wire:navigate>
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($inventaire->statut === 'brouillon' || $inventaire->statut === 'en_cours')
                        <a href="{{ route('inventaires.mouvements', $inventaire->id) }}" class="btn btn-sm btn-primary" wire:navigate>
                            <i class="fas fa-list"></i>
                        </a>
                        @endif
                        <button wire:click="supprimer({{ $inventaire->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cet inventaire ?" class="btn btn-sm btn-error">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Aucun inventaire trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $inventaires->links() }}
    </div>
</div>