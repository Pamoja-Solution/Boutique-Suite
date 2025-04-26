<?php
namespace App\Livewire;

use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class GestionStockSimple extends Component
{
    use WithPagination;

    public $search = '';
    public $produitSelectionne = null;
    
    // Champs de modification
    public $quantiteAjout = 0;
    public $nouveauPrixVente = null;
    public $nouveauPrixAchat = null;
    public $nouvelleDateExpiration = null;
    
    // Pour affichage seulement
    public $stockActuel = 0;
    public $nouveauStockCalcule = null;

    public function selectionnerProduit($produitId)
{
    $this->resetExcept('search', 'produits');
    $this->produitSelectionne = Produit::findOrFail($produitId);
    $this->stockActuel = $this->produitSelectionne->stock;
    
    // Réinitialiser les champs à null ou vide
    $this->nouveauPrixVente = null;
    $this->nouveauPrixAchat = null;
    $this->quantiteAjout = 0;
    $this->nouveauStockCalcule = null;
    
    // Gestion correcte de la date (peut être string ou Carbon)
    $this->nouvelleDateExpiration = $this->produitSelectionne->date_expiration 
        ? (is_string($this->produitSelectionne->date_expiration) 
            ? $this->produitSelectionne->date_expiration 
            : $this->produitSelectionne->date_expiration->format('Y-m-d'))
        : null;
}

    public function updatedProduitSelectionne()
    {
        if ($this->produitSelectionne) {
            $this->stockActuel = $this->produitSelectionne->stock;
            $this->nouveauPrixVente = $this->produitSelectionne->prix_vente;
            $this->nouveauPrixAchat = $this->produitSelectionne->prix_achat;
            $this->nouvelleDateExpiration = $this->produitSelectionne->date_expiration?->format('Y-m-d');
        }
    }

    public function calculerStock()
    {
        $this->validate([
            'quantiteAjout' => 'required|integer',
            'nouveauPrixVente' => 'nullable|numeric|min:0',
            'nouveauPrixAchat' => 'nullable|numeric|min:0',
            'nouvelleDateExpiration' => 'nullable|date'
        ]);
        
        $this->nouveauStockCalcule = $this->stockActuel + $this->quantiteAjout;
    }

    public function sauvegarderModifications()
    {
        $this->validate([
            'quantiteAjout' => 'required|integer',
            'nouveauPrixVente' => 'nullable|numeric|min:0',
            'nouveauPrixAchat' => 'nullable|numeric|min:0',
            'nouvelleDateExpiration' => 'nullable|date'
        ]);

        DB::transaction(function () {
            // Mise à jour du stock
            $this->produitSelectionne->stock += $this->quantiteAjout;
            
            // Mise à jour conditionnelle des prix
            if ($this->nouveauPrixVente !== null) {
                $this->produitSelectionne->prix_vente = $this->nouveauPrixVente;
            }
            
            if ($this->nouveauPrixAchat !== null) {
                $this->produitSelectionne->prix_achat = $this->nouveauPrixAchat;
            }
            
            // Mise à jour date expiration si nécessaire
            if ($this->produitSelectionne->date_expiration || $this->nouvelleDateExpiration) {
                $this->produitSelectionne->date_expiration = $this->nouvelleDateExpiration;
            }
            
            $this->produitSelectionne->save();
            
            // Réinitialisation
            $this->selectionnerProduit($this->produitSelectionne->id);
            session()->flash('success', 'Modifications enregistrées avec succès!');
        });
    }

    public function render()
    {
        $produits = Produit::query()
            ->with(['fournisseur', 'sousRayon'])
            ->when($this->search, function($query) {
                $query->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('reference_interne', 'like', '%'.$this->search.'%');
            })
            ->orderBy('nom')
            ->paginate(10);

        return view('livewire.gestion-stock-simple', [
            'produits' => $produits,
            'produitSelectionne' => $this->produitSelectionne
        ]);
    }
}