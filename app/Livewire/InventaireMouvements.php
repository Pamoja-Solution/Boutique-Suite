<?php

namespace App\Livewire;

use App\Models\Inventaire;
use App\Models\MouvementInventaire;
use App\Models\Produit;
use App\Models\SousRayon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class InventaireMouvements extends Component
{
    use WithPagination;
    
    public $inventaireId;
    public $inventaire;
    public $search = '';
    public $selectedSousRayon = '';
    public $produitsResults;
    
    public function mount($inventaireId)
    {
        $this->inventaireId = $inventaireId;
        $this->inventaire = Inventaire::findOrFail($inventaireId);
        
        if ($this->inventaire->statut === 'brouillon') {
            $this->inventaire->update(['statut' => 'en_cours']);
        }
        
        $this->produitsResults = new Collection();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'selectedSousRayon'])) {
            $this->loadProduitsResults();
        }
    }

    public function loadProduitsResults()
    {
        if (empty($this->search)) {
            $this->produitsResults = new Collection();
            return;
        }
        
        $query = Produit::query()
            ->with('sousRayon.rayon')
            ->when($this->search, function($q) {
                $q->where('nom', 'like', "%{$this->search}%")
                  ->orWhere('reference_interne', 'like', "%{$this->search}%")
                  ->orWhere('code_barre', 'like', "%{$this->search}%");
            })
            ->when($this->selectedSousRayon, function($q) {
                $q->where('sous_rayon_id', $this->selectedSousRayon);
            });
            
        $mouvementsProduitsIds = MouvementInventaire::where('inventaire_id', $this->inventaireId)
            ->pluck('produit_id');
            
        $this->produitsResults = $query->whereNotIn('id', $mouvementsProduitsIds)
            ->limit(20)
            ->get();
    }

    public function ajouterProduit($produitId)
    {
        $produit = Produit::findOrFail($produitId);
        
        MouvementInventaire::firstOrCreate(
            ['inventaire_id' => $this->inventaireId, 'produit_id' => $produit->id],
            ['quantite_theorique' => $produit->stock]
        );
        
        $this->loadProduitsResults();
        $this->dispatch('toast', type: 'success', message: 'Produit ajouté à l\'inventaire');
    }
    
    public function getNombreProduitsProperty()
    {
        return MouvementInventaire::where('inventaire_id', $this->inventaireId)->count();
    }
    
    public function updateQuantiteReelle($mouvementId, $value)
    {
        MouvementInventaire::where('id', $mouvementId)
            ->update(['quantite_reelle' => $value]);
    }
    
    public function retirerProduit($mouvementId)
    {
        MouvementInventaire::where('id', $mouvementId)->delete();
        $this->loadProduitsResults();
        $this->dispatch('toast', type: 'success', message: 'Produit retiré');
    }
    
    public function finaliserInventaire()
    {
        $mouvementsNonRemplis = MouvementInventaire::where('inventaire_id', $this->inventaireId)
            ->whereNull('quantite_reelle')
            ->exists();
            
        if ($mouvementsNonRemplis) {
            $this->dispatch('toast', type: 'error', 
                message: 'Veuillez renseigner toutes les quantités réelles avant de finaliser');
            return;
        }
        
        $this->inventaire->update(['statut' => 'terminé']);
        $this->dispatch('toast', type: 'success', message: 'Inventaire finalisé');
        $this->redirectRoute('inventaires.index');
    }
    
    public function render()
    {
        $sousRayons = SousRayon::with('rayon')->get();
        
        $mouvements = MouvementInventaire::where('inventaire_id', $this->inventaireId)
            ->with(['produit.sousRayon.rayon'])
            ->when($this->search, function ($query) {
                $query->whereHas('produit', function ($q) {
                    $q->where('nom', 'like', "%{$this->search}%")
                      ->orWhere('reference_interne', 'like', "%{$this->search}%")
                      ->orWhere('code_barre', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedSousRayon, function ($query) {
                $query->whereHas('produit', function ($q) {
                    $q->where('sous_rayon_id', $this->selectedSousRayon);
                });
            })
            ->paginate(10);
        
        return view('livewire.inventaire-mouvements', [
            'mouvementsListe' => $mouvements,
            'sousRayons' => $sousRayons,
            'peutFinaliser' => $this->inventaire->statut === 'en_cours',
        ]);
    }
}