<div>
    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())
    @include('gerant.nav')
    @endif
    @section('titre','Station de Base')

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Modification des Ventes du Jour</h1>

        @if(session('message'))
        <div class="alert alert-success mb-4">
            {{ session('message') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left column - Today's sales -->
            <div class="bg-base-100 p-4 rounded-box shadow">
                <h2 class="text-xl font-semibold mb-4">Ventes du {{ now()->format('d/m/Y') }}</h2>

                <div class="space-y-3">
                    @forelse($ventes as $vente)
                    <div wire:click="selectVente({{ $vente->id }})"
                        class="p-3 border rounded-box cursor-pointer hover:bg-base-200 transition 
                                   {{ $selectedVente && $selectedVente->id == $vente->id ? 'bg-info/10 border-info' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">#{{ $vente->matricule }}</p>
                                <p class="text-sm text-base-content/60">Client : {{ $vente->client->nom }}</p>
                                <p class="textarea-md font-bold"> Vendeur : {{ $vente->user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">{{ number_format($vente->total, 2, ',', ' ') }} FC</p>
                                <p class="text-xs text-base-content/50">{{ $vente->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-base-content/50">Aucune vente enregistrée aujourd'hui.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right column - Sale details and modification -->
            <div class="lg:col-span-2 bg-base-100 p-4 rounded-box shadow">
                @if($selectedVente)
                <h2 class="text-xl font-semibold mb-4">Modification de la vente #{{ $selectedVente->matricule }}</h2>

                <!--div class="mb-4">
                        <label class="label">
                            <span class="label-text">Notes (optionnel)</span>
                        </label>
                        <textarea 
                            wire: model="clientNotes"
                            class="textarea textarea-bordered w-full focus:outline-none focus:ring-1 focus:ring-info"
                            rows="2"
                            placeholder="Raison de la modification..."
                        ></textarea>
                    </div-->

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-right">Prix Unitaire</th>
                                <th class="text-right">Quantité Originale</th>
                                <th class="text-right">Quantité Modifiée</th>
                                <th class="text-right">Différence</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $index => $detail)
                            <tr>
                                <td>{{ $detail['produit_nom'] }}</td>
                                <td class="text-right">{{ number_format($detail['prix_unitaire'], 2, ',', ' ') }} FC
                                </td>
                                <td class="text-right">{{ $detail['quantite_originale'] }}</td>
                                <td>
                                    <input type="number" wire:model="details.{{ $index }}.quantite_modifiee"
                                        wire:change="updateQuantity({{ $index }})" min="0"
                                        class="input input-bordered w-full focus:outline-none focus:ring-1 focus:ring-info">
                                </td>
                                <td
                                    class="text-right {{ $detail['difference'] > 0 ? 'text-success' : ($detail['difference'] < 0 ? 'text-error' : '') }}">
                                    {{ $detail['difference'] }}
                                </td>
                                <td class="text-center">
                                    <button wire:click="removeProduct({{ $index }})"
                                        class="btn btn-circle btn-sm btn-error" title="Supprimer ce produit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-base-200 font-semibold">
                                <td colspan="4" class="text-right">Total:</td>
                                <td class="text-right" colspan="2">
                                    {{ number_format(array_reduce($details, function($carry, $item) {
                                    return $carry + ($item['quantite_modifiee'] * $item['prix_unitaire']);
                                    }, 0), 2, ',', ' ') }} FC
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button class="btn btn-primary btn-sm"
                        onclick="window.open('{{ route('ventes.print-invoice', ['vente' => $selectedVente->id]) }}', '_blank')">
                        Imprimer
                    </button>
                    <button wire:click="saveModifications" class="btn btn-primary">
                        Enregistrer les Modifications
                    </button>
                </div>
                @else
                <div class="text-center py-8 text-base-content/50">
                    <p>Sélectionnez une vente à gauche pour voir les détails et effectuer des modifications.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>