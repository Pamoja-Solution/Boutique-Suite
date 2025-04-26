<?php

namespace App\Livewire;

use App\Models\Monnaie;
use App\Models\TauxChange;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TauxChangeManager extends Component
{
    use WithPagination;

    public $taux_id;
    public $monnaie_source_id;
    public $monnaie_cible_id;
    public $taux;
    public $date_effet;
    public $isOpen = false;
    public $searchTerm = '';
    public $confirmingDelete = false;
    public $deleteId = null;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        
        $tauxChanges = TauxChange::with(['monnaieSource', 'monnaieCible'])
            ->where(function($query) use ($searchTerm) {
                $query->whereHas('monnaieSource', function($q) use ($searchTerm) {
                    $q->where('libelle', 'like', $searchTerm)
                      ->orWhere('code', 'like', $searchTerm);
                })
                ->orWhereHas('monnaieCible', function($q) use ($searchTerm) {
                    $q->where('libelle', 'like', $searchTerm)
                      ->orWhere('code', 'like', $searchTerm);
                });
            })
            ->orderBy('date_effet', 'desc')
            ->paginate(10);
            
        $monnaies = Monnaie::orderBy('libelle')->get();
            
        return view('livewire.taux-change-manager', [
            'tauxChanges' => $tauxChanges,
            'monnaies' => $monnaies
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->date_effet = date('Y-m-d');
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
        $this->taux_id = null;
        $this->monnaie_source_id = '';
        $this->monnaie_cible_id = '';
        $this->taux = '';
        $this->date_effet = '';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'monnaie_source_id' => 'required|exists:monnaies,id|different:monnaie_cible_id',
            'monnaie_cible_id' => 'required|exists:monnaies,id|different:monnaie_source_id',
            'taux' => 'required|numeric|min:0.000001',
            'date_effet' => [
                'required',
                'date',
                Rule::unique('taux_change', 'date_effet')
                    ->where('monnaie_source_id', $this->monnaie_source_id)
                    ->where('monnaie_cible_id', $this->monnaie_cible_id)
                    ->ignore($this->taux_id)
            ],
        ]);

        TauxChange::updateOrCreate(
            ['id' => $this->taux_id],
            [
                'monnaie_source_id' => $this->monnaie_source_id,
                'monnaie_cible_id' => $this->monnaie_cible_id,
                'taux' => $this->taux,
                'date_effet' => $this->date_effet,
            ]
        );

        session()->flash('message', $this->taux_id 
            ? 'Taux de change mis à jour avec succès.' 
            : 'Taux de change créé avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $tauxChange = TauxChange::findOrFail($id);
        $this->taux_id = $id;
        $this->monnaie_source_id = $tauxChange->monnaie_source_id;
        $this->monnaie_cible_id = $tauxChange->monnaie_cible_id;
        $this->taux = $tauxChange->taux;
        $this->date_effet = $tauxChange->date_effet->format('Y-m-d');
        
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
            TauxChange::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Taux de change supprimé avec succès.');
        }
        
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }
}