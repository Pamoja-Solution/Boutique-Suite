<?php

// app/Http/Livewire/VendeurMedicaments.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicament;
use App\Models\Vente;
use App\Models\Client;
use App\Models\DetailVente;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class VendeurMedicaments extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedMedicaments = [];
    public $quantites = [];
    public $clientId;
    public $showVenteModal = false;
    public $clients = [];

    protected $rules = [
        'clientId' => 'required|exists:clients,id',
        'selectedMedicaments' => 'required|array|min:1',
        'selectedMedicaments.*' => 'exists:medicaments,id',
        'quantites.*' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->clients = Client::all();
    }

    public function render()
    {
        $medicaments = Medicament::where('nom', 'like', '%'.$this->search.'%')
            ->orWhere('description', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.vendeur-medicaments', [
            'medicaments' => $medicaments,
        ]);
    }

    public function addToCart($medicamentId)
    {
        if (!isset($this->selectedMedicaments[$medicamentId])) {
            $this->selectedMedicaments[$medicamentId] = 1;
            $this->quantites[$medicamentId] = 1;
        }
    }

    public function removeFromCart($medicamentId)
    {
        unset($this->selectedMedicaments[$medicamentId]);
        unset($this->quantites[$medicamentId]);
    }

    public function finaliserVente()
    {
        $this->validate();

        // Créer la vente
        $vente = Vente::create([
            'client_id' => $this->clientId,
            'total' => $this->calculateTotal(),
            'user_id' => Auth::user()->id,
        ]);

        // Ajouter les détails de la vente
        foreach ($this->selectedMedicaments as $medicamentId => $selected) {
            $medicament = Medicament::find($medicamentId);
            
            DetailVente::create([
                'vente_id' => $vente->id,
                'medicament_id' => $medicamentId,
                'quantite' => $this->quantites[$medicamentId],
                'prix_unitaire' => $medicament->prix_vente,
            ]);

            // Mettre à jour le stock
            $medicament->decrement('stock', $this->quantites[$medicamentId]);
        }

        // Réinitialiser le panier
        $this->reset(['selectedMedicaments', 'quantites', 'clientId', 'showVenteModal']);
        $this->emit('venteSuccess', 'Vente enregistrée avec succès!');
    }

    private function calculateTotal()
    {
        $total = 0;
        foreach ($this->selectedMedicaments as $medicamentId => $selected) {
            $medicament = Medicament::find($medicamentId);
            $total += $medicament->prix_vente * $this->quantites[$medicamentId];
        }
        return $total;
    }

    public function getTotalProperty()
    {
        return $this->calculateTotal();
    }
}
