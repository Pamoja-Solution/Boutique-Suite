<?php

namespace App\Livewire;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\DetailVente;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VenteManager extends Component
{
    use WithPagination;

    public $client_id, $total = 0, $medicaments = [], $vente_id;
    public $selected_medicament_id, $quantite, $showDetails = false;
    public $currentVente;
    public $search = '';

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'medicaments' => 'required|array|min:1',
        'medicaments.*.medicament_id' => 'required|exists:medicaments,id',
        'medicaments.*.quantite' => 'required|integer|min:1',
    ];

    public function render()
    {
        return view('livewire.vente-manager', [
            'ventes' => Vente::when($this->search, function($query) {
                        $query->whereHas('client', function($q) {
                            $q->where('nom', 'like', "%{$this->search}%");
                        })->orWhere('matricule', 'like', "%{$this->search}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10),
            'clients' => Client::all(),
            'medicamentsDisponibles' => Medicament::where('stock', '>', 0)->get(),
        ]);
    }

    public function createVente()
    {
        $this->resetInputFields();
        $this->emit('createVente');
    }

    public function addMedicament()
    {
        $this->validate([
            'selected_medicament_id' => 'required|exists:medicaments,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $medicament = Medicament::find($this->selected_medicament_id);
        
        if ($medicament->stock < $this->quantite) {
            session()->flash('error', 'Stock insuffisant pour ' . $medicament->nom);
            return;
        }

        // Vérifier si le médicament est déjà dans la liste
        $exists = false;
        foreach ($this->medicaments as $key => $item) {
            if ($item['medicament_id'] == $this->selected_medicament_id) {
                $this->medicaments[$key]['quantite'] += $this->quantite;
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $this->medicaments[] = [
                'medicament_id' => $this->selected_medicament_id,
                'nom' => $medicament->nom,
                'prix' => $medicament->prix_vente,
                'quantite' => $this->quantite,
            ];
        }

        $this->calculerTotal();
        $this->selected_medicament_id = '';
        $this->quantite = '';
    }

    public function removeMedicament($index)
    {
        unset($this->medicaments[$index]);
        $this->medicaments = array_values($this->medicaments);
        $this->calculerTotal();
    }

    public function calculerTotal()
    {
        $this->total = 0;
        foreach ($this->medicaments as $item) {
            $this->total += $item['prix'] * $item['quantite'];
        }
    }

    public function storeVente()
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'medicaments' => 'required|array|min:1',
        ]);

        // Créer la vente
        $vente = Vente::create([
            'client_id' => $this->client_id,
            'user_id' => Auth::id(),
            'total' => $this->total,
            'matricule' => 'VNT-' . strtoupper(Str::random(6)),
        ]);

        // Créer les détails de vente et mettre à jour le stock
        foreach ($this->medicaments as $item) {
            DetailVente::create([
                'vente_id' => $vente->id,
                'medicament_id' => $item['medicament_id'],
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix'],
            ]);

            // Mettre à jour le stock
            $medicament = Medicament::find($item['medicament_id']);
            $medicament->update([
                'stock' => $medicament->stock - $item['quantite']
            ]);
        }

        session()->flash('message', 'Vente enregistrée avec succès.');
        $this->resetInputFields();
        $this->emit('venteCreated');
    }

    public function showVenteDetails($id)
    {
        $this->currentVente = Vente::with(['detailsVentes.medicament', 'client', 'user'])->find($id);
        $this->showDetails = true;
    }

    public function hideDetails()
    {
        $this->showDetails = false;
    }

    private function resetInputFields()
    {
        $this->client_id = '';
        $this->total = 0;
        $this->medicaments = [];
        $this->selected_medicament_id = '';
        $this->quantite = '';
        $this->vente_id = null;
    }
}