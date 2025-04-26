<?php

namespace App\Livewire;

use App\Models\TauxChange;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TauxChangeScroller extends Component
{
    public function render()
    {
        // On récupère les derniers taux d'échange
        $tauxChanges = TauxChange::with(['monnaieSource', 'monnaieCible'])
            ->latest('date_effet')
            ->limit(20)
            ->get();

        return view('livewire.taux-change-scroller', [
            'tauxChanges' => $tauxChanges
        ]);
    }
}
