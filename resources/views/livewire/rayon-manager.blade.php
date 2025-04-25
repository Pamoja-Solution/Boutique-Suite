<div class="">
    @include('gerant.nav')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 dark:bg-gray-800">
                <!-- En-tête avec onglets -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <button 
                            :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400': $wire.activeTab === 'rayons', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': $wire.activeTab !== 'rayons' }" 
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            wire:click="switchTab('rayons')"
                        >
                            Rayons
                        </button>
                        <button 
                            :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400': $wire.activeTab === 'sousRayons', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': $wire.activeTab !== 'sousRayons' }" 
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            wire:click="switchTab('sousRayons')"
                        >
                            Sous-Rayons
                        </button>
                    </nav>
                </div>
    
                <!-- Barre de recherche et bouton d'ajout -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $activeTab === 'rayons' ? 'Gestion des Rayons' : 'Gestion des Sous-Rayons' }}
                    </h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" wire:model.live="search" placeholder="Rechercher..." 
                                class="rounded-md dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <button 
                            wire:click="{{ $activeTab === 'rayons' ? 'createRayon' : 'createSousRayon' }}" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition dark:hover:bg-indigo-700 dark:focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter
                        </button>
                    </div>
                </div>
    
                <!-- Messages de session -->
                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-800 dark:border-green-600 dark:text-green-100" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif
    
                <!-- Contenu des onglets -->
                @if($activeTab === 'rayons')
                    <!-- Tableau des rayons -->
                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Code
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Icône
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Ordre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-600">
                                @forelse ($rayons as $rayon)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $rayon->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $rayon->nom }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($rayon->description, 30) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-{{ $rayon->icon }}"></i>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $rayon->ordre_affichage }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button wire:click="editRayon({{ $rayon->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteRayon({{ $rayon->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Supprimer" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce rayon ?') || event.stopImmediatePropagation()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
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
                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Code
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Rayon parent
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-600">
                                @forelse ($sousRayons as $sousRayon)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $sousRayon->code_emplacement }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $sousRayon->nom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($sousRayon->rayon)
                                                {{ $sousRayon->rayon->nom }} ({{ $sousRayon->rayon->code }})
                                            @else
                                                Non assigné
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button wire:click="editSousRayon({{ $sousRayon->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteSousRayon({{ $sousRayon->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Supprimer" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce sous-rayon ?') || event.stopImmediatePropagation()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
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
            <div class="fixed inset-0 overflow-y-auto z-50">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800">
                        @if($action === 'createRayon' || $action === 'editRayon')
                            <form wire:submit.prevent="{{ $action === 'createRayon' ? 'storeRayon' : 'updateRayon' }}">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        {{ $action === 'createRayon' ? 'Créer un nouveau rayon' : 'Modifier le rayon' }}
                                    </h3>
                                    
                                    <div class="mb-4">
                                        <label for="rayon_nom" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Nom:</label>
                                        <input type="text" id="rayon_nom" wire:model="rayon_nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('rayon_nom') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="rayon_code" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Code:</label>
                                            <input type="text" id="rayon_code" wire:model="rayon_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                            @error('rayon_code') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="rayon_icon" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Icône:</label>
                                            <select id="rayon_icon" wire:model="rayon_icon" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
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
                                            @error('rayon_icon') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="rayon_description" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Description:</label>
                                        <textarea id="rayon_description" wire:model="rayon_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500"></textarea>
                                        @error('rayon_description') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="rayon_ordre" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Ordre d'affichage:</label>
                                        <input type="number" id="rayon_ordre" wire:model="rayon_ordre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('rayon_ordre') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-800">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:hover:bg-indigo-700 dark:focus:ring-indigo-500">
                                        {{ $action === 'createRayon' ? 'Créer' : 'Mettre à jour' }}
                                    </button>
                                    <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        @elseif($action === 'createSousRayon' || $action === 'editSousRayon')
                            <form wire:submit.prevent="{{ $action === 'createSousRayon' ? 'storeSousRayon' : 'updateSousRayon' }}">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        {{ $action === 'createSousRayon' ? 'Créer un nouveau sous-rayon' : 'Modifier le sous-rayon' }}
                                    </h3>
                                    
                                    <div class="mb-4">
                                        <label for="sous_rayon_nom" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Nom:</label>
                                        <input type="text" id="sous_rayon_nom" wire:model="sous_rayon_nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('sous_rayon_nom') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="sous_rayon_code" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Code emplacement:</label>
                                            <input type="text" id="sous_rayon_code" wire:model="sous_rayon_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                            @error('sous_rayon_code') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="sous_rayon_rayon_id" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Rayon parent:</label>
                                            <select id="sous_rayon_rayon_id" wire:model="sous_rayon_rayon_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                                <option value="">Sélectionner un rayon</option>
                                                @foreach($allRayons as $rayon)
                                                    <option value="{{ $rayon->id }}">{{ $rayon->nom }}</option>
                                                @endforeach
                                            </select>
                                            @error('sous_rayon_rayon_id') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-800">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:hover:bg-indigo-700 dark:focus:ring-indigo-500">
                                        {{ $action === 'createSousRayon' ? 'Créer' : 'Mettre à jour' }}
                                    </button>
                                    <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>