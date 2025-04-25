<?php

namespace App\Livewire;

use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Rayon;
use App\Models\SousRayon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ProduitManager extends Component
{
    use WithPagination;

    public $nom, $description, $prix_vente, $prix_achat, $stock, $seuil_alerte;
    public $fournisseur_id, $rayon_id, $sous_rayon_id, $produit_id, $reference_interne;
    public $code_barre, $unite_mesure = 'unité', $taxable = true;
    public $isOpen = false;
    public $action = '';
    public $search = '';
    public $stock_a_ajouter = 0;
    public $sousRayons = []; // Pour stocker les sous-rayons dynamiques

    protected $rules = [
        'nom' => 'required|string|max:255',
        'prix_vente' => 'required|numeric|min:0',
        'prix_achat' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'seuil_alerte' => 'required|integer|min:0',
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'rayon_id' => 'required|exists:rayons,id',
        'sous_rayon_id' => 'required|exists:sous_rayons,id',
        'unite_mesure' => 'required|string',
        'taxable' => 'required|boolean',
    ];

    public function updatedRayonId($value)
{
    $this->sous_rayon_id = null;
    $this->sousRayons = SousRayon::where('rayon_id', $value)->get();
}


    public function render()
    {
                abort_if(!Auth::user()?->isGerant(), 403);  

        return view('livewire.produit-manager', [
            'produits' => Produit::where('nom', 'like', "%{$this->search}%")
                            ->orWhere('reference_interne', 'like', "%{$this->search}%")
                            ->orWhere('code_barre', 'like', "%{$this->search}%")
                            ->paginate(10),
            'fournisseurs' => Fournisseur::all(),
            'rayons' => Rayon::all(),
            'sousRayons' => $this->rayon_id 
                            ? SousRayon::where('rayon_id', $this->rayon_id)->get() 
                            : collect(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->action = 'create';
        $this->openModal();
         // Charger les sous-rayons si rayon_id est déjà défini
        if ($this->rayon_id) {
            $this->sousRayons = SousRayon::where('rayon_id', $this->rayon_id)->get();
        }
    }

    public function addStock($id)
    {
        $this->produit_id = $id;
        $this->action = 'addStock';
        $this->openModal();
    }

    public function saveStock()
    {
        $this->validate([
            'stock_a_ajouter' => 'required|integer|min:1',
        ]);

        $produit = Produit::find($this->produit_id);
        $produit->update([
            'stock' => $produit->stock + $this->stock_a_ajouter
        ]);

        session()->flash('message', 'Stock ajouté avec succès.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function store()
    {
        $this->validate();

        Produit::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat,
            'stock' => $this->stock,
            'seuil_alerte' => $this->seuil_alerte,
            'fournisseur_id' => $this->fournisseur_id,
            'rayon_id' => $this->rayon_id,
            'sous_rayon_id' => $this->sous_rayon_id,
            'reference_interne' => $this->reference_interne ?? Produit::generateReference(),
            'code_barre' => $this->code_barre,
            'unite_mesure' => $this->unite_mesure,
            'taxable' => $this->taxable,
        ]);

        session()->flash('message', 'Produit créé avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $produit = Produit::findOrFail($id);
        $this->produit_id = $id;
        $this->nom = $produit->nom;
        $this->description = $produit->description;
        $this->prix_vente = $produit->prix_vente;
        $this->prix_achat = $produit->prix_achat;
        $this->stock = $produit->stock;
        $this->seuil_alerte = $produit->seuil_alerte;
        $this->fournisseur_id = $produit->fournisseur_id;
        $this->rayon_id = $produit->rayon_id;
        $this->sous_rayon_id = $produit->sous_rayon_id;
        $this->reference_interne = $produit->reference_interne;
        $this->code_barre = $produit->code_barre;
        $this->unite_mesure = $produit->unite_mesure;
        $this->taxable = $produit->taxable;
        // Charger les sous-rayons correspondants
        if ($this->rayon_id) {
            $this->sousRayons = SousRayon::where('rayon_id', $this->rayon_id)->get();
        }

        $this->action = 'edit';
        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        $produit = Produit::find($this->produit_id);
        $produit->update([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat,
            'stock' => $this->stock,
            'seuil_alerte' => $this->seuil_alerte,
            'fournisseur_id' => $this->fournisseur_id,
            'rayon_id' => $this->rayon_id,
            'sous_rayon_id' => $this->sous_rayon_id,
            'reference_interne' => $this->reference_interne,
            'code_barre' => $this->code_barre,
            'unite_mesure' => $this->unite_mesure,
            'taxable' => $this->taxable,
        ]);

        session()->flash('message', 'Produit mis à jour avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Produit::find($id)->delete();
        session()->flash('message', 'Produit supprimé avec succès.');
    }

    private function resetInputFields()
    {
        $this->reset([
            'nom', 'description', 'prix_vente', 'prix_achat', 'stock', 'seuil_alerte',
            'fournisseur_id', 'rayon_id', 'sous_rayon_id', 'produit_id', 'reference_interne',
            'code_barre', 'unite_mesure', 'taxable', 'stock_a_ajouter'
        ]);
        $this->unite_mesure = 'unité';
        $this->taxable = true;
        $this->sousRayons = collect(); // Réinitialiser la liste des sous-rayons

    }
}