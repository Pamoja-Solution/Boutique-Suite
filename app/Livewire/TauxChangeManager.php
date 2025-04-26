<?php
namespace App\Livewire;

use App\Models\Monnaie;
use App\Models\TauxChange;
use Livewire\Component;
use Livewire\WithPagination;

class TauxChangeManager extends Component
{
    use WithPagination;

    public $monnaie_id, $taux, $date_effet;
    public $tauxChangeId;
    public $search = '';
    public $isModalOpen = false;

    protected $rules = [
        'monnaie_id' => 'required|exists:monnaies,id',
        'taux' => 'required|numeric|min:0.000001',
        'date_effet' => 'required|date|unique:taux_change,date_effet,NULL,id,monnaie_id,'.$this->monnaie_id
    ];
    public function resetInputFields()
    {
        $this->taux = '';
        $this->date_effet = '';
    }
    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }


    public function render()
    {
        $tauxChanges = TauxChange::with('monnaie')
            ->when($this->search, function($query) {
                $query->whereHas('monnaie', function($q) {
                    $q->where('libelle', 'like', '%'.$this->search.'%')
                      ->orWhere('code', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('date_effet', 'desc')
            ->paginate(10);

        $monnaies = Monnaie::all();

        return view('livewire.taux-change-manager', [
            'tauxChanges' => $tauxChanges,
            'monnaies' => $monnaies
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->date_effet = now()->format('Y-m-d');
        $this->openModal();
    }

    // ... (méthodes openModal, closeModal, resetInputFields similaires à MonnaiesManager)

    public function store()
    {
        $this->validate();

        TauxChange::updateOrCreate(['id' => $this->tauxChangeId], [
            'monnaie_id' => $this->monnaie_id,
            'taux' => $this->taux,
            'date_effet' => $this->date_effet
        ]);

        session()->flash('message', 
            $this->tauxChangeId ? 'Taux mis à jour.' : 'Taux créé.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $tauxChange = TauxChange::findOrFail($id);
        $this->tauxChangeId = $id;
        $this->monnaie_id = $tauxChange->monnaie_id;
        $this->taux = $tauxChange->taux;
        $this->date_effet = $tauxChange->date_effet->format('Y-m-d');
        
        $this->openModal();
    }

    public function delete($id)
    {
        TauxChange::find($id)->delete();
        session()->flash('message', 'Taux supprimé.');
    }
}