<div class="">
    @section("titre", 'Gestion Clients')
    @include('gerant.nav')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100  overflow-hidden shadow-xl rounded-box p-6">
                <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="alert alert-success mb-4">
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Header and Search -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Gestion des Clients</h2>
                    <div class="flex gap-4">
                        <div>
                            <input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                placeholder="Rechercher..." 
                                class="input input-bordered dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                            >
                        </div>
                        <button 
                            wire:click="create" 
                            class="btn btn-primary"
                        >
                            Ajouter un client
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto mt-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Date de naissance</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($clients as $client)
                            <tr wire:key="user-card-{{ $client->id }}-{{ $loop->index }}">
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->nom }}</td>
                                <td>{{ $client->telephone ?: '-' }}</td>
                                <td>
                                    {{ $client->date_naissance ? date('d/m/Y', strtotime($client->date_naissance)) : '-' }}
                                </td>
                                <td>{{ $client->email ?: '-' }}</td>
                                <td>{{ $client->adresse ?: '-' }}</td>
                                <td class="flex gap-2">
                                    <button 
                                        wire:click="edit({{ $client->id }})" 
                                        class="btn btn-warning btn-sm"
                                    >
                                        Modifier
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $client->id }})" 
                                        class="btn btn-error btn-sm"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun client trouvé</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $clients->links() }}
                </div>

                <!-- Form Modal -->
                @if($isOpen)
                <div class="modal modal-open">
                    <div class="modal-box max-w-3xl">
                        <h3 class="font-bold text-lg">
                            {{ $clientId ? 'Modifier' : 'Ajouter' }} un client
                        </h3>
                        <form wire:submit.prevent="store">
                            <div class="space-y-4 py-4">
                                <div class="form-control">
                                    <label for="nom" class="label">
                                        <span class="label-text">Nom</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nom" 
                                        wire:model="nom" 
                                        class="input input-bordered"
                                    >
                                    @error('nom') <span class="text-error text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-control">
                                    <label for="telephone" class="label">
                                        <span class="label-text">Téléphone</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="telephone" 
                                        wire:model="telephone" 
                                        class="input input-bordered"
                                    >
                                    @error('telephone') <span class="text-error text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-control">
                                    <label for="date_naissance" class="label">
                                        <span class="label-text">Date de naissance</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="date_naissance" 
                                        wire:model="date_naissance" 
                                        class="input input-bordered"
                                    >
                                    @error('date_naissance') <span class="text-error text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-control">
                                    <label for="email" class="label">
                                        <span class="label-text">Email</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        wire:model="email" 
                                        class="input input-bordered"
                                    >
                                    @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-control">
                                    <label for="adresse" class="label">
                                        <span class="label-text">Adresse</span>
                                    </label>
                                    <textarea 
                                        id="adresse" 
                                        wire:model="adresse" 
                                        rows="3"
                                        class="textarea textarea-bordered"
                                    ></textarea>
                                    @error('adresse') <span class="text-error text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="modal-action">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                >
                                    {{ $clientId ? 'Mettre à jour' : 'Enregistrer' }}
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="closeModal" 
                                    class="btn"
                                >
                                    Annuler
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
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 ml-4 text-left">
                                <h3 class="text-lg leading-6 font-medium">
                                    Confirmer la suppression
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm">
                                        Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-action">
                            <button 
                                type="button" 
                                wire:click="delete" 
                                class="btn btn-error"
                            >
                                Supprimer
                            </button>
                            <button 
                                type="button" 
                                wire:click="cancelDelete" 
                                class="btn"
                            >
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