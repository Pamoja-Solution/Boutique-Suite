<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Monnaie;
use App\Models\TauxChange;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionTauxChange extends Component
{
    public $monnaieBase;
    public $monnaieCible;
    public $taux;
    public $dateEffet;
    public $historique = [];

    protected $rules = [
        'monnaieBase' => 'required|exists:monnaies,id',
        'monnaieCible' => 'required|exists:monnaies,id|different:monnaieBase',
        'taux' => 'required|numeric|min:0.000001',
        'dateEffet' => 'required|date',
    ];

    public function mount()
    {
        $this->dateEffet = now()->format('Y-m-d');
    }

    public function saveTaux()
    {
        $this->validate();

        TauxChange::updateOrCreate(
            [
                'monnaie_base_id' => $this->monnaieBase,
                'monnaie_cible_id' => $this->monnaieCible,
                'date_effet' => $this->dateEffet,
            ],
            ['taux' => $this->taux]
        );

        $this->loadHistorique();
        session()->flash('success', 'Taux de change enregistré.');
    }

    public function loadHistorique()
    {
        $this->historique = TauxChange::with(['base', 'cible'])
            ->where(function($q) {
                $q->where('monnaie_base_id', $this->monnaieBase)
                  ->where('monnaie_cible_id', $this->monnaieCible);
            })
            ->orWhere(function($q) {
                $q->where('monnaie_base_id', $this->monnaieCible)
                  ->where('monnaie_cible_id', $this->monnaieBase);
            })
            ->orderBy('date_effet', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.gestion-taux-change', [
            'monnaies' => Monnaie::orderBy('libelle')->get(),
        ]);
    }
}