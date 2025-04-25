<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionClients extends Component
{
    use WithPagination,AuthorizesRequests;

    public $search = '';
    public $clientId = null;
    public $nom = '';
    public $telephone = '';
    public $date_naissance = '';
    public $email = '';
    public $adresse = '';
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $clientToDelete = null;

    public function render()
    {
        //abort_if(!Auth::user()?->isGerant(), 403);  

        $clients = Client::where('nom', 'like', '%' . $this->search . '%')
            ->orWhere('telephone', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('adresse', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.gestion-clients', [
            'clients' => $clients
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
        $this->clientId = null;
        $this->nom = '';
        $this->telephone = '';
        $this->date_naissance = '';
        $this->email = '';
        $this->adresse = '';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'nom' => 'required|min:2',
            'telephone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'date_naissance' => 'nullable|date',
            'email' => 'nullable|email',
            'adresse' => 'nullable',
        ]);

        Client::updateOrCreate(['id' => $this->clientId], [
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'date_naissance' => $this->date_naissance,
            'email' => $this->email,
            'adresse' => $this->adresse,
        ]);

        session()->flash('message', $this->clientId ? 'Client mis à jour avec succès.' : 'Client créé avec succès.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $this->clientId = $id;
        $this->nom = $client->nom;
        $this->telephone = $client->telephone;
        $this->date_naissance = $client->date_naissance;
        $this->email = $client->email;
        $this->adresse = $client->adresse;
    
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->clientToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->clientToDelete = null;
    }
    
    public function delete()
    {
        Client::find($this->clientToDelete)->delete();
        session()->flash('message', 'Client supprimé avec succès.');
        $this->confirmingDeletion = false;
        $this->clientToDelete = null;
    }
}