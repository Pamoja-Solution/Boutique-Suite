@section("titre", "Taux d'échange")
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @include("gerant.nav")
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des taux de change</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Configurez et suivez l'évolution des taux de change entre devises
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Mettre à jour depuis API
                </button>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ajouter un nouveau taux</h2>
            
            <form wire:submit.prevent="saveTaux">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Devise de base -->
                    <div>
                        <label for="monnaieBase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Devise de base <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="monnaieBase"
                            wire:model="monnaieBase" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm"
                        >
                            <option value="">Sélectionnez une devise</option>
                            @foreach($monnaies as $monnaie)
                                <option value="{{ $monnaie->id }}">
                                    {{ $monnaie->code }} - {{ $monnaie->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('monnaieBase')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Devise cible -->
                    <div>
                        <label for="monnaieCible" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Devise cible <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="monnaieCible"
                            wire:model="monnaieCible" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm"
                        >
                            <option value="">Sélectionnez une devise</option>
                            @foreach($monnaies as $monnaie)
                                <option value="{{ $monnaie->id }}">
                                    {{ $monnaie->code }} - {{ $monnaie->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('monnaieCible')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Taux -->
                    <div>
                        <label for="taux" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Taux <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input 
                                type="number" 
                                id="taux"
                                step="0.000001"
                                wire:model="taux" 
                                class="block w-full pr-12 pl-3 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                placeholder="0.000000"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">
                                    {{ $monnaieCible ? Monnaie::find($monnaieCible)?->code : '---' }}
                                </span>
                            </div>
                        </div>
                        @error('taux')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Date effet -->
                    <div>
                        <label for="dateEffet" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date d'effet <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="dateEffet"
                            wire:model="dateEffet" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm"
                        >
                        @error('dateEffet')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Enregistrer le taux
                    </button>
                </div>
            </form>
        </div>

        <!-- Historique -->
        @if($monnaieBase && $monnaieCible)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Historique des taux
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            (1 {{ Monnaie::find($monnaieBase)?->code }} → {{ Monnaie::find($monnaieCible)?->code }})
                        </span>
                    </h2>
                    
                    <div class="flex items-center space-x-3">
                        <div class="relative rounded-md shadow-sm">
                            <input 
                                type="text" 
                                class="block w-full pr-10 pl-3 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                placeholder="Rechercher..."
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Taux de change
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse($historique as $taux)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $taux->date_effet->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $taux->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <span class="font-semibold">1 {{ $taux->base->code }}</span> = 
                                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ number_format($taux->taux, 6) }}</span> {{ $taux->cible->code }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($taux->date_effet <= now()->format('Y-m-d'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                                Actif
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                                Futur
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <button 
                                                wire:click="deleteTaux({{ $taux->id }})" 
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="confirm('Êtes-vous sûr de vouloir supprimer ce taux ?') || event.stopImmediatePropagation()"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun taux enregistré</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commencez par ajouter un nouveau taux de change.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($historique->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $historique->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>