<div>
    @section("titre","Gestion d'Utilisateurs")

    @include('gerant.nav')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl rounded-box p-6">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold text-base-content">Gestion des Utilisateurs</h2>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <div class="relative flex-grow">
                            <label class="input input-bordered flex items-center gap-2">
                                <input type="text" wire:model.live="search" placeholder="Rechercher..." 
                                    class="grow dark:placeholder-gray-400">
                                <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </label>
                        </div>
                        <button wire:click="create" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter
                        </button>
                    </div>
                </div>
    
                <!-- Success Message -->
                @if (session()->has('message'))
                    <div role="alert" class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
    
                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr class="bg-base-200">
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Matricule</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="hover:bg-base-200" wire:key="user-card-{{ $user->id }}-{{ $loop->index }}">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar">
                                                <div class="mask mask-squircle w-10 h-10">
                                                    @if($user->image)
                                                        <img src="{{ $user->image }}" alt="{{ $user->name }}">
                                                    @else
                                                        <svg class="w-10 h-10 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->matricule }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $user->role === 'superviseur' ? 'badge-primary' : 
                                               ($user->role === 'gerant' ? 'badge-secondary' : 'badge-neutral') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status ? 'badge-success' : 'badge-error' }}">
                                            {{ $user->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            
                                            <button wire:click="edit({{ $user->id }})" class="btn btn-sm  btn-ghost text-info">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $user->id }})" class="btn btn-sm  btn-ghost text-error" 
                                                onclick="confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?') || event.stopImmediatePropagation()">
                                                <svg  class="size-[1.2em]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-base-content/70">
                                        Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    
        <!-- Modal Form -->
        @if($isOpen)
            <div class="modal modal-open">
                <div class="modal-box max-w-2xl">
                    <h3 class="font-bold text-lg mb-4">
                        {{ $action == 'create' ? 'Créer un utilisateur' : 'Modifier utilisateur' }}
                    </h3>
                    <form wire:submit.prevent="{{ $action == 'create' ? 'store' : 'update' }}">
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Matricule</span>
                                </label>
                                <input type="text" wire:model.defer="matricule" class="input input-bordered" disabled>
                                @error('matricule') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom</span>
                                </label>
                                <input type="text" wire:model.defer="name" class="input input-bordered">
                                @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input type="email" wire:model.defer="email" class="input input-bordered">
                                @error('email') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Mot de passe{{ $action == 'edit' ? ' (laisser vide pour conserver)' : '' }}</span>
                                </label>
                                <input type="password" wire:model.defer="password" class="input input-bordered">
                                @error('password') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Rôle</span>
                                </label>
                                <select wire:model.defer="role" class="select select-bordered">
                                    <option value="vendeur">Vendeur</option>
                                    <option value="gerant">Gérant</option>
                                    <option value="superviseur">Superviseur</option>
                                </select>
                                @error('role') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Statut</span>
                                </label>
                                <select wire:model.defer="status" class="select select-bordered">
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                                @error('status') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-action">
                            <button type="button" wire:click="closeModal" class="btn">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                {{ $action == 'create' ? 'Créer' : 'Mettre à jour' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>