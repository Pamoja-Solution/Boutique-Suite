<?php
namespace App\Livewire;

use App\Models\Monnaie;
use Livewire\Component;
use Livewire\WithPagination;

class MonnaieManager extends Component
{
    use WithPagination;

    public $libelle, $symbole, $code, $taux_change = 1.0, $statut = 0;
    public $monnaieId;
    public $search = '';
    public $isModalOpen = false;

    protected $rules = [
        'libelle' => 'required|string|max:255|unique:monnaies,libelle',
        'symbole' => 'required|string|max:10|unique:monnaies,symbole',
        'code' => 'required|string|size:3|unique:monnaies,code',
        'taux_change' => 'required|numeric|min:0.000001',
        'statut' => 'boolean'
    ];

    public function render()
    {
        $monnaies = Monnaie::when($this->search, function($query) {
            $query->where('libelle', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
        })->paginate(10);

        return view('livewire.monnaie-manager', ['monnaies' => $monnaies]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function resetInputFields()
    {
        $this->libelle = '';
        $this->symbole = '';
        $this->code = '';
        $this->taux_change = 1.0;
        $this->statut = 0;
        $this->monnaieId = null;
    }

    public function store()
    {
        $this->validate();

        Monnaie::updateOrCreate(['id' => $this->monnaieId], [
            'libelle' => $this->libelle,
            'symbole' => $this->symbole,
            'code' => strtoupper($this->code),
            'taux_change' => $this->taux_change,
            'statut' => $this->statut
        ]);

        session()->flash('message', 
            $this->monnaieId ? 'Monnaie mise à jour.' : 'Monnaie créée.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $monnaie = Monnaie::findOrFail($id);
        $this->monnaieId = $id;
        $this->libelle = $monnaie->libelle;
        $this->symbole = $monnaie->symbole;
        $this->code = $monnaie->code;
        $this->taux_change = $monnaie->taux_change;
        $this->statut = $monnaie->statut;
        
        $this->openModal();
    }

    public function delete($id)
    {
        Monnaie::find($id)->delete();
        session()->flash('message', 'Monnaie supprimée.');
    }

    public function toggleStatus($id)
    {
        $monnaie = Monnaie::find($id);
        $monnaie->statut = !$monnaie->statut;
        $monnaie->save();
    }
}