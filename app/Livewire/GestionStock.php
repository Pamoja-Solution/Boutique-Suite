<?php
namespace App\Livewire;

use App\Models\Produit;
use App\Models\StockMouvement;
use Livewire\Component;
use Livewire\WithPagination;

class GestionStock extends Component
{
    use WithPagination;

    public $search = '';
    public $produitSelectionne = null;
    public $modificationEnCours = false;
    
    // Données de modification
    public $quantiteAjout = 0;
    public $nouveauPrixVente;
    public $nouveauPrixAchat;
    public $nouvelleDateExpiration;
    public $raisonModification = '';

    protected $rules = [
        'quantiteAjout' => 'required|integer',
        'nouveauPrixVente' => 'nullable|numeric|min:0',
        'nouveauPrixAchat' => 'nullable|numeric|min:0',
        'nouvelleDateExpiration' => 'nullable|date',
        'raisonModification' => 'required|string|max:255'
    ];

    public function selectionnerProduit($produitId)
    {
        $this->produitSelectionne = Produit::find($produitId);
        $this->quantiteAjout = 0;
        $this->nouveauPrixVente = $this->produitSelectionne->prix_vente;
        $this->nouveauPrixAchat = $this->produitSelectionne->prix_achat;
        $this->nouvelleDateExpiration = $this->produitSelectionne->date_expiration?->format('Y-m-d');
        $this->modificationEnCours = false;
    }

    public function previewModification()
    {
        $this->validate();
        $this->modificationEnCours = true;
    }

    public function appliquerModification()
    {
        $this->validate();

        DB::transaction(function () {
            $produit = $this->produitSelectionne;
            $ancienStock = $produit->stock;
            $nouveauStock = $ancienStock + $this->quantiteAjout;

            // Créer le mouvement de stock
            StockMouvement::create([
                'produit_id' => $produit->id,
                'quantite_avant' => $ancienStock,
                'quantite_apres' => $nouveauStock,
                'prix_avant' => $produit->prix_vente,
                'prix_apres' => $this->nouveauPrixVente ?? $produit->prix_vente,
                'date_expiration_avant' => $produit->date_expiration,
                'date_expiration_apres' => $this->nouvelleDateExpiration,
                'type_mouvement' => $this->quantiteAjout >= 0 ? 'ajout' : 'retrait',
                'raison' => $this->raisonModification,
                'user_id' => auth()->id()
            ]);

            // Mettre à jour le produit
            $produit->stock = $nouveauStock;
            if ($this->nouveauPrixVente) $produit->prix_vente = $this->nouveauPrixVente;
            if ($this->nouveauPrixAchat) $produit->prix_achat = $this->nouveauPrixAchat;
            if ($this->nouvelleDateExpiration) $produit->date_expiration = $this->nouvelleDateExpiration;
            $produit->save();

            // Réinitialiser
            $this->reset(['quantiteAjout', 'modificationEnCours', 'raisonModification']);
            $this->selectionnerProduit($produit->id);
            session()->flash('success', 'Modifications appliquées avec succès!');
        });
    }

    public function render()
    {
        $produits = Produit::query()
            ->when($this->search, fn($q) => $q->where('nom', 'like', "%{$this->search}%")
            ->orWhere('reference_interne', 'like', "%{$this->search}%"))
            ->with(['fournisseur', 'sousRayon'])
            ->paginate(10);

        $historique = $this->produitSelectionne 
            ? StockMouvement::where('produit_id', $this->produitSelectionne->id)
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('livewire.gestion-stock', [
            'produits' => $produits,
            'historique' => $historique,
            'produitSelectionne' => $this->produitSelectionne,
        ]);
    }
}