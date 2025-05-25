<?php

namespace App\Livewire;

use App\Models\Monnaie;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TauxChangeScroller extends Component
{
    public function render()
    {
        // On récupère toutes les monnaies avec leur taux de change (par rapport au CDF)
        $monnaies = Monnaie::where('code', '!=', 'CDF') // Exclure le franc congolais lui-même
            ->where('taux_change', '>', 0) // Uniquement celles avec un taux défini
            ->where('statut', 1) // Uniquement les monnaies actives
            ->orderBy('code')
            ->get();

        return view('livewire.taux-change-scroller', [
            'monnaies' => $monnaies
        ]);
    }
}