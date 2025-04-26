<?php

namespace App\Livewire;

use App\Models\Monnaie;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MonnaieManager extends Component
{
    use WithPagination;

    public $monnaie_id;
    public $libelle;
    public $symbole;
    public $code;
    public $statut = '0';
    public $isOpen = false;
    public $searchTerm = '';
    public $confirmingDelete = false;
    public $deleteId = null;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        
        $monnaies = Monnaie::where('libelle', 'like', $searchTerm)
            ->orWhere('code', 'like', $searchTerm)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('livewire.monnaie-manager', [
            'monnaies' => $monnaies
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
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->monnaie_id = null;
        $this->libelle = '';
        $this->symbole = '';
        $this->code = '';
        $this->statut = '0';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'libelle' => ['required', Rule::unique('monnaies', 'libelle')->ignore($this->monnaie_id)],
            'symbole' => ['required', Rule::unique('monnaies', 'symbole')->ignore($this->monnaie_id)],
            'code' => ['required', 'max:10', Rule::unique('monnaies', 'code')->ignore($this->monnaie_id)],
            'statut' => 'required|in:0,1',
        ]);

        Monnaie::updateOrCreate(
            ['id' => $this->monnaie_id],
            [
                'libelle' => $this->libelle,
                'symbole' => $this->symbole,
                'code' => strtoupper($this->code),
                'taux_change' => 0,
                'statut' => $this->statut,
            ]
        );

        session()->flash('message', $this->monnaie_id 
            ? 'Monnaie mise à jour avec succès.' 
            : 'Monnaie créée avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $monnaie = Monnaie::findOrFail($id);
        $this->monnaie_id = $id;
        $this->libelle = $monnaie->libelle;
        $this->symbole = $monnaie->symbole;
        $this->code = $monnaie->code;
        $this->statut = $monnaie->statut;
        
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Monnaie::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Monnaie supprimée avec succès.');
        }
        
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }
}