<?php

namespace App\Livewire;

use App\Models\Medicament;
use App\Models\Fournisseur;
use App\Models\Categorie;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class MedicamentManager extends Component
{
    use WithPagination;

    public $nom, $description, $prix_vente, $prix_achat, $stock, $date_expiration;
    public $fournisseur_id, $categorie_id, $medicament_id, $matricule;
    public $isOpen = false;
    public $action = '';
    public $search = '';
    public $stock_a_ajouter = 0;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'required|string',
        'prix_vente' => 'required|numeric|min:0',
        'prix_achat' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'date_expiration' => 'required|date|after:today',
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'categorie_id' => 'required|exists:categories,id',
    ];

    public function render()
    {
        return view('livewire.medicament-manager', [
            'medicaments' => Medicament::where('nom', 'like', "%{$this->search}%")
                            ->orWhere('matricule', 'like', "%{$this->search}%")
                            ->paginate(10),
            'fournisseurs' => Fournisseur::all(),
            'categories' => Categorie::all(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->action = 'create';
        $this->openModal();
    }

    public function addStock($id)
    {
        $this->medicament_id = $id;
        $this->action = 'addStock';
        $this->openModal();
    }

    public function saveStock()
    {
        $this->validate([
            'stock_a_ajouter' => 'required|integer|min:1',
        ]);

        $medicament = Medicament::find($this->medicament_id);
        $medicament->update([
            'stock' => $medicament->stock + $this->stock_a_ajouter
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

        // Génération du matricule
       // $this->matricule = 'MED-' . strtoupper(Str::random(6));
       $matricule = Medicament::generateMatricule(); // Appel manuel
        Medicament::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat,
            'stock' => $this->stock,
            'date_expiration' => $this->date_expiration,
            'fournisseur_id' => $this->fournisseur_id,
            'categorie_id' => $this->categorie_id,
            //'matricule' => $this->matricule,
            'matricule' => $matricule, 
        ]);

        session()->flash('message', 'Médicament créé avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $medicament = Medicament::findOrFail($id);
        $this->medicament_id = $id;
        $this->nom = $medicament->nom;
        $this->description = $medicament->description;
        $this->prix_vente = $medicament->prix_vente;
        $this->prix_achat = $medicament->prix_achat;
        $this->stock = $medicament->stock;
        $this->date_expiration = $medicament->date_expiration;
        $this->fournisseur_id = $medicament->fournisseur_id;
        $this->categorie_id = $medicament->categorie_id;
        //$this->matricule = $medicament->matricule;

        $this->action = 'edit';
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix_vente' => 'required|numeric|min:0',
            'prix_achat' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'date_expiration' => 'required|date',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        $medicament = Medicament::find($this->medicament_id);
        $medicament->update([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat,
            'stock' => $this->stock,
            'date_expiration' => $this->date_expiration,
            'fournisseur_id' => $this->fournisseur_id,
            'categorie_id' => $this->categorie_id,
        ]);

        session()->flash('message', 'Médicament mis à jour avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Medicament::find($id)->delete();
        session()->flash('message', 'Médicament supprimé avec succès.');
    }

    private function resetInputFields()
    {
        $this->nom = '';
        $this->description = '';
        $this->prix_vente = '';
        $this->prix_achat = '';
        $this->stock = '';
        $this->date_expiration = '';
        $this->fournisseur_id = '';
        $this->categorie_id = '';
        $this->medicament_id = null;
        $this->matricule = '';
        $this->stock_a_ajouter = 0;
    }
}