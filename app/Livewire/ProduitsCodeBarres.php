<?php
// app/Livewire/ProduitsCodeBarres.php

namespace App\Livewire;

use App\Models\Produit;
use App\Services\BarcodeService;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ProduitsCodeBarres extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedProduits = [];
    public $editingProduit = null;
    public $editingCodeBarre = '';
    public $showModal = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectAll()
    {
        $produits = $this->getProduitsQuery()->get();
        $this->selectedProduits = $produits->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedProduits = [];
    }

    public function generateCodeBarre($produitId)
    {
        $produit = Produit::find($produitId);
        if ($produit) {
            $produit->generateCodeBarre();
            $this->dispatch('code-barre-generated');
        }
    }

    public function editCodeBarre($produitId)
    {
        $this->editingProduit = Produit::find($produitId);
        $this->editingCodeBarre = $this->editingProduit->code_barre ?? '';
        $this->showModal = true;
    }

    public function updateCodeBarre()
    {
        $this->validate([
            'editingCodeBarre' => 'required|string|max:50|unique:produits,code_barre,' . $this->editingProduit->id,
        ]);

        $this->editingProduit->update(['code_barre' => $this->editingCodeBarre]);
        $this->closeModal();
        $this->dispatch('code-barre-updated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingProduit = null;
        $this->editingCodeBarre = '';
    }

    public function printSingle($produitId)
    {
        $produit = Produit::find($produitId);
        if ($produit && $produit->code_barre) {
            // Convertir le produit unique en une collection
            $produitsCollection = new Collection([$produit]);
            return $this->generatePDF($produitsCollection);
        }
    }

    public function printSelected()
    {
        if (!empty($this->selectedProduits)) {
            $produits = Produit::whereIn('id', $this->selectedProduits)
                ->whereNotNull('code_barre')
                ->get();
            return $this->generatePDF($produits);
        }
    }

    private function generatePDF($produits)
{
    $barcodeService = new BarcodeService();
    
    if (!$produits instanceof Collection) {
        $produits = new Collection($produits);
    }
    
    // Augmentez ces valeurs si nécessaire
    $baseHeight = 150; // mm
    $perItemHeight = 5; // mm (augmenté de 3 à 5)
    $totalHeight = $baseHeight + (count($produits) * $perItemHeight);
    
    $widthInPoints = 80 * 2.83;
    $heightInPoints = $totalHeight * 2.83;

    $produitsWithBarcodes = $produits->map(function ($produit) use ($barcodeService) {
        return [
            'nom' => $produit->nom,
            'code_barre' => $produit->code_barre,
            'prix_vente' => $produit->prix_vente,
            'reference_interne' => $produit->reference_interne,
            'barcode_image' => $barcodeService->generateBarcodePNG($produit->code_barre),
        ];
    });

    $pdf = Pdf::loadView('pdf.codes-barres', [
        'produits' => $produitsWithBarcodes
    ])
    ->setPaper([0, 0, $widthInPoints, $heightInPoints], 'portrait')
    ->setOption('margin-top', 0)
    ->setOption('margin-bottom', 0)
    ->setOption('margin-left', 0)
    ->setOption('margin-right', 0)
    ->setOption('enable_remote', true)
    ->setOption('isPhpEnabled', true)
    ->setOption('isHtml5ParserEnabled', true);

    return response()->streamDownload(
        fn () => print($pdf->output()),
        'codes-barres-' . now()->format('Y-m-d-H-i-s') . '.pdf'
    );
}
    private function getProduitsQuery()
    {
        return Produit::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('code_barre', 'like', '%' . $this->search . '%')
                  ->orWhere('reference_interne', 'like', '%' . $this->search . '%');
            });
        })->orderBy('nom');
    }

    public function render()
    {
        $produits = $this->getProduitsQuery()->paginate(10);
        $barcodeService = new BarcodeService();

        // Ajouter les codes-barres SVG pour l'affichage
        $produits->getCollection()->transform(function ($produit) use ($barcodeService) {
            if ($produit->code_barre) {
                $produit->barcode_svg = $barcodeService->generateBarcodeSVG($produit->code_barre);
            }
            return $produit;
        });

        return view('livewire.produits-code-barres', [
            'produits' => $produits,
        ])->layout('layouts.app');
    }
}