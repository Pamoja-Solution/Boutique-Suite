<?php

namespace App\Livewire;

use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionFournisseurs extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $fournisseurId = null;
    public $nom = '';
    public $adresse = '';
    public $telephone = '';
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $fournisseurToDelete = null;

    public function render()
    {
        //dd(Auth::user());
        abort_if(!auth()->user()?->isGerant(), 403);  
        $fournisseurs = Fournisseur::where('nom', 'like', '%' . $this->search . '%')
            ->orWhere('adresse', 'like', '%' . $this->search . '%')
            ->orWhere('telephone', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.gestion-fournisseurs', [
            'fournisseurs' => $fournisseurs
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->fournisseurId = null;
        $this->nom = '';
        $this->adresse = '';
        $this->telephone = '';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'nom' => 'required|min:2',
            'adresse' => 'nullable',
            'telephone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
        ]);

        Fournisseur::updateOrCreate(['id' => $this->fournisseurId], [
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
        ]);

        session()->flash('message', $this->fournisseurId ? 'Fournisseur mis à jour avec succès.' : 'Fournisseur créé avec succès.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $this->fournisseurId = $id;
        $this->nom = $fournisseur->nom;
        $this->adresse = $fournisseur->adresse;
        $this->telephone = $fournisseur->telephone;
    
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->fournisseurToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->fournisseurToDelete = null;
    }
    
    public function delete()
    {
        Fournisseur::find($this->fournisseurToDelete)->delete();
        session()->flash('message', 'Fournisseur supprimé avec succès.');
        $this->confirmingDeletion = false;
        $this->fournisseurToDelete = null;
    }
}