<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Exemple : layouts/app.blade.php
class UserManager extends Component
{
    use WithPagination, AuthorizesRequests;

    public $name, $email, $password, $role, $status, $user_id,$matricule;
    public $isOpen = false;
    public $action = '';
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'matricule' => '|matricule|unique:users,matricule',
        'password' => 'required|min:8',
        'role' => 'required|in:vendeur,gerant,superviseur',
        'status' => 'required|boolean',
    ];

    public function render()
    {
       //dd(Auth::user());
        try {
            Gate::define('manage-users', function (User $user) {
                return $user->isGerant();
            });        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect('/')->with('error', 'Accès non autorisé');
        }    
        //$this->authorize('manage-users');
        
        return view('livewire.user-manager', [
            'users' => User::where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                       ->paginate(10),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->action = 'create';
        $this->openModal();
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

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Utilisateur créé avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->password = '';
        $this->matricule=$user->matricule;

        $this->action = 'edit';
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->user_id,
            'role' => 'required|in:vendeur,gerant,superviseur',
            'status' => 'required|boolean',
        ]);

        $user = User::find($this->user_id);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
        ]);

        if (!empty($this->password)) {
            $user->update([
                'password' => bcrypt($this->password),
            ]);
        }

        session()->flash('message', 'Utilisateur mis à jour avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Utilisateur supprimé avec succès.');
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'vendeur';
        $this->status = 0;
        $this->user_id = null;
    }
}