<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class Modifications extends Component
{
    public $ventes; // Ventes du jours
    public $selectedVente = null; // Vente selectionnée
    public $details = []; // Details de la vente pour modification
    //public $clientNotes = ''; // Notes du client
    
    // Initialisation du composant
    public function mount()
    {
        $this->loadTodaySales();
    }
    
    // Charger les ventes du jour
    protected function loadTodaySales()
    {
        $this->ventes = Vente::with(['client', 'user'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    // Selectionner une vente pour modification
    public function selectVente($venteId)
    {
        $this->selectedVente = Vente::with(['client', 'details.produit'])->find($venteId);
        $this->details = $this->selectedVente->details->map(function($detail) {
            return [
                'id' => $detail->id,
                'produit_id' => $detail->produit_id,
                'produit_nom' => $detail->produit->nom,
                'quantite_originale' => $detail->quantite,
                'quantite_modifiee' => $detail->quantite,
                'prix_unitaire' => $detail->prix_unitaire,
                'difference' => 0,
            ];
        })->toArray();
        
        //$this->clientNotes = $this->selectedVente->notes ?? '';
    }
    
    // Mettre à jour la quantité d'un produit dans la vente
    public function updateQuantity($index)
    {
        $this->details[$index]['difference'] = 
            $this->details[$index]['quantite_originale'] - $this->details[$index]['quantite_modifiee'];
    }
    
    // Supprimer un produit de la vente
    public function removeProduct($index)
    {
        $productToRemove = $this->details[$index];
        
        DB::transaction(function() use ($productToRemove) {
            // Supprimer le detail de la vente
            DetailVente::find($productToRemove['id'])->delete();
            
            // Retourner la quantité totale au stock
            $produit = Produit::find($productToRemove['produit_id']);
            $produit->stock += $productToRemove['quantite_originale'];
            $produit->save();
        });
        
        // Supprimer du tableau local
        unset($this->details[$index]);
        $this->details = array_values($this->details); // Reindex array
        
        // Rafraîchir la vente sélectionnée pour obtenir le total mis à jour
        $this->selectVente($this->selectedVente->id);
        
        session()->flash('message', 'Produit supprimé de la facture avec succès.');
    }
    
    // Enregistrer les modifications de la vente
    public function saveModifications()
    {
        $this->validate([
            'details.*.quantite_modifiee' => 'required|numeric|min:0',
            //'clientNotes' => 'nullable|string',
        ]);
        
        // Commencer une transaction
        DB::transaction(function() {
            foreach ($this->details as $detail) {
                $originalQty = $detail['quantite_originale'];
                $modifiedQty = $detail['quantite_modifiee'];
                $difference = $originalQty - $modifiedQty;
                
                if ($difference != 0) {
                    // Mettre à jour le detail de la vente
                    $detailVente = DetailVente::find($detail['id']);
                    $detailVente->quantite = $modifiedQty;
                    $detailVente->save();
                    
                    // Mettre à jour le stock du produit
                    $produit = Produit::find($detail['produit_id']);
                    $produit->stock += $difference;
                    $produit->save();
                }
            }
            
            // Mettre à jour le total de la vente et les notes
            $this->selectedVente->total = collect($this->details)->sum(function($item) {
                return $item['quantite_modifiee'] * $item['prix_unitaire'];
            });
            
            //$this->selectedVente->notes = $this->clientNotes;
            $this->selectedVente->save();
        });
        
        // Rafraîchir les données
        $this->loadTodaySales();
        $this->selectVente($this->selectedVente->id);
        
        session()->flash('message', 'Modifications enregistrées avec succès.');
    }
    
    public function render()
    {
        return view('livewire.modifications');
    }
}