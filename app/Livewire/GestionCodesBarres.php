<?php

namespace App\Livewire;

use App\Models\Produit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionCodesBarres extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    public $selectedProduits = [];
    public $selectAll = false;
    public $produitEdit;
    public $showEditModal = false;
    public $newCodeBarre;

    // Options d'impression
    public $nombreParLigne = 4;
    public $tailleCodeBarre = 'medium';

    public function render()
    {
        $produits = Produit::query()
            ->when($this->search, fn($query) => 
                $query->where('nom', 'like', '%'.$this->search.'%')
                    ->orWhere('code_barre', 'like', '%'.$this->search.'%')
                    ->orWhere('reference_interne', 'like', '%'.$this->search.'%'))
            ->orderBy('nom')
            ->paginate(20);

        return view('livewire.gestion-codes-barres', [
            'produits' => $produits,
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProduits = $this->produits->pluck('id')->toArray();
        } else {
            $this->selectedProduits = [];
        }
    }

    public function editCodeBarre($produitId)
    {
        $this->produitEdit = Produit::find($produitId);
        $this->newCodeBarre = $this->produitEdit->code_barre;
        $this->showEditModal = true;
    }

    public function updateCodeBarre()
    {
        $this->validate([
            'newCodeBarre' => 'nullable|string|max:255|unique:produits,code_barre,'.$this->produitEdit->id,
        ]);

        $this->produitEdit->update(['code_barre' => $this->newCodeBarre]);
        $this->showEditModal = false;
        $this->dispatch('notify', ['message' => 'Code barre mis à jour avec succès!']);
    }

    public function imprimerCodeBarre($produitId)
    {
        $produit = Produit::find($produitId);
        $pdf = Pdf::loadView('pdf.code-barre-single', [
            'produit' => $produit,
            'taille' => $this->tailleCodeBarre,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "code-barre-{$produit->reference_interne}.pdf"
        );
    }

    public function imprimerSelection()
    {
        if (empty($this->selectedProduits)) {
            $this->dispatch('notify', ['message' => 'Aucun produit sélectionné', 'type' => 'error']);
            return;
        }

        $produits = Produit::whereIn('id', $this->selectedProduits)->get();
        
        $pdf = Pdf::loadView('pdf.code-barre-multiple', [
            'produits' => $produits,
            'nombreParLigne' => $this->nombreParLigne,
            'taille' => $this->tailleCodeBarre,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "codes-barres-selection-".now()->format('Ymd-His').".pdf"
        );
    }
}