<x-app-layout>

@section('titre', 'Liste des devises')


<div class="max-w-7xl mx-auto px-4 py-6 ">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Liste des devises</h4>
            <h6 class="text-gray-600"></h6>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6 ">
        <!-- Formulaire -->
        <div class="w-full md:w-5/12 ">
            <form id="formSend" action="{{route('monnaie.store')}}" method="POST" class="bg-white p-6 rounded-lg shadow-md dark:bg-gray-700">
                @csrf
                <div class="mb-4 ">
                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200 " >
                        Nom de la devise <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" id="nom" value="{{old('nom')}}" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nom') border-red-500 @enderror" 
                           placeholder="Saisir le nom" required />
                    @error('nom')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-6">
                    <button type="submit" id="btnSend" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau -->
        <div class="w-full md:w-7/12">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 dark:bg-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-4">
                            <button class="p-2 rounded-md bg-gray-100 hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </button>
                            <button class="p-2 rounded-md bg-blue-500 hover:bg-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto dark:bg-gray-700">
                        <p class="text-red-500 mb-4">Vous ne pouvez activer qu'une seule devise</p>
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Devises</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-600">
                                @forelse ($monnaies as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-500">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{$item->libelle}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->statut)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activé</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Desactivé</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{route('monnaie.edit', $item->id)}}" class="text-blue-600 hover:text-blue-900 mr-4">
                                            <i class="fas fa-edit mr-1"></i> Modifier
                                        </a>
                                        <a href="{{route('monnaie.active', $item->id)}}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-money-check-alt mr-1"></i> 
                                            @if($item->statut)
                                            Desactiver
                                            @else
                                            Activer
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <img src="{{ asset('app-assets/images/picto_landlord_search.png') }}" class="h-24 mb-4" alt="Aucun résultat">
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">Il n'y a rien par ici...</h4>
                                            <p class="text-gray-500 mb-4">Cette page permet de gérer les monnaies.</p>
                                            <a href="#" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                                Ajouter la devise
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</x-app-layout>
