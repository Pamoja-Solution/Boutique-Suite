<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Gestion des Médicaments</h2>
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
                    <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition dark:hover:bg-indigo-700 dark:focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter
                    </button>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-800 dark:border-green-600 dark:text-green-100" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Matricule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Nom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Prix Vente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Stock
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Date d'expiration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-600">
                        @forelse ($medicaments as $medicament)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $medicament->matricule }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $medicament->nom }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($medicament->description, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($medicament->prix_vente, 2) }} Fc
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $medicament->stock > 20 ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                          ($medicament->stock > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                        {{ $medicament->stock }} unités
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($medicament->date_expiration)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="addStock({{ $medicament->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="edit({{ $medicament->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $medicament->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce médicament ?') || event.stopImmediatePropagation()">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
                                    Aucun médicament trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $medicaments->links() }}
            </div>
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
                    @if($action == 'addStock')
                        <form wire:submit.prevent="saveStock">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                                <div class="mb-4">
                                    <label for="stock_a_ajouter" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Quantité à ajouter au stock:</label>
                                    <input type="number" id="stock_a_ajouter" wire:model.defer="stock_a_ajouter" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500" min="1">
                                    @error('stock_a_ajouter') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-800">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm dark:hover:bg-green-700 dark:focus:ring-green-500">
                                    Ajouter au stock
                                </button>
                                <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    @else
                        <form wire:submit.prevent="{{ $action == 'create' ? 'store' : 'update' }}">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                                <div class="mb-4">
                                    <label for="nom" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Nom:</label>
                                    <input type="text" id="nom" wire:model.defer="nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                    @error('nom') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Description:</label>
                                    <textarea id="description" wire:model.defer="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500"></textarea>
                                    @error('description') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="prix_vente" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Prix de vente (Fc):</label>
                                        <input type="number" id="prix_vente" wire:model.defer="prix_vente" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('prix_vente') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="prix_achat" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Prix d'achat (Fc):</label>
                                        <input type="number" id="prix_achat" wire:model.defer="prix_achat" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('prix_achat') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="stock" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Stock:</label>
                                        <input type="number" id="stock" wire:model.defer="stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('stock') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="date_expiration" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Date d'expiration:</label>
                                        <input type="date" id="date_expiration" wire:model.defer="date_expiration" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        @error('date_expiration') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="fournisseur_id" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Fournisseur:</label>
                                    <select id="fournisseur_id" wire:model.defer="fournisseur_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        <option value="">Sélectionner un fournisseur</option>
                                        @foreach($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('fournisseur_id') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="categorie_id" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Catégorie:</label>
                                    <select id="categorie_id" wire:model.defer="categorie_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-indigo-500">
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('categorie_id') <span class="text-red-500 text-xs dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-800">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:hover:bg-indigo-700 dark:focus:ring-indigo-500">
                                    {{ $action == 'create' ? 'Créer' : 'Mettre à jour' }}
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