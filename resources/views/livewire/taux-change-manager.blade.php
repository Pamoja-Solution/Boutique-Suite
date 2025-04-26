<!-- Tableau des taux de change -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Monnaie Source
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Monnaie Cible
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Taux
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Date d'effet
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @forelse($tauxChanges as $tauxChange)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $tauxChange->monnaieSource->libelle }} ({{ $tauxChange->monnaieSource->code }})
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $tauxChange->monnaieCible->libelle }} ({{ $tauxChange->monnaieCible->code }})
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $tauxChange->taux }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $tauxChange->date_effet ? \Carbon\Carbon::parse($tauxChange->date_effet)->format('d/m/Y') : 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="edit({{ $tauxChange->id }})" 
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                            Modifier
                        </button>
                        <button wire:click="confirmDelete({{ $tauxChange->id }})" 
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            Supprimer
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Aucun taux de change trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Dans le modal de formulaire -->
<div class="mb-4">
    <label for="monnaie_source_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monnaie Source</label>
    <select id="monnaie_source_id" wire:model="monnaie_source_id" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">Sélectionner une monnaie source</option>
        @foreach($monnaies as $monnaie)
            <option value="{{ $monnaie->id }}">{{ $monnaie->libelle }} ({{ $monnaie->code }})</option>
        @endforeach
    </select>
    @error('monnaie_source_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="monnaie_cible_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monnaie Cible</label>
    <select id="monnaie_cible_id" wire:model="monnaie_cible_id" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">Sélectionner une monnaie cible</option>
        @foreach($monnaies as $monnaie)
            <option value="{{ $monnaie->id }}">{{ $monnaie->libelle }} ({{ $monnaie->code }})</option>
        @endforeach
    </select>
    @error('monnaie_cible_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>