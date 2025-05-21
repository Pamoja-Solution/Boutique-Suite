<?php

namespace App\Livewire;

use App\Models\Inventaire;
use App\Models\Produit;
use App\Models\MouvementInventaire;
use App\Models\HistoriqueStock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventaireForm extends Component
{
    public $inventaireId;
    public $reference;
    public $motif;
    public $notes;
    public $statut = 'brouillon';
    
    public $isEdit = false;
    public $isTermine = false;
    
    public function mount($inventaireId = null)
    {
        if ($inventaireId) {
            $this->inventaireId = $inventaireId;
            $this->isEdit = true;
            $inventaire = Inventaire::findOrFail($inventaireId);
            $this->reference = $inventaire->reference;
            $this->motif = $inventaire->motif;
            $this->notes = $inventaire->notes;
            $this->statut = $inventaire->statut;
            $this->isTermine = $inventaire->statut === 'terminé';
        } else {
            $this->reference = 'INV-' . date('YmdHis');
        }
    }
    
    protected function rules()
    {
        return [
            'reference' => 'required|string|max:255|unique:inventaires,reference,' . $this->inventaireId,
            'motif' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'statut' => 'required|in:brouillon,en_cours,terminé,annulé',
        ];
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            if ($this->isEdit) {
                $inventaire = Inventaire::findOrFail($this->inventaireId);
                
                if ($inventaire->statut === 'terminé' && $this->statut !== 'terminé') {
                    $this->dispatch('toast', type: 'error', message: 'Impossible de modifier le statut d\'un inventaire terminé');
                    DB::rollBack();
                    return;
                }
                
                $inventaire->update([
                    'reference' => $this->reference,
                    'motif' => $this->motif,
                    'notes' => $this->notes,
                    'statut' => $this->statut,
                ]);
                
                // Si le statut passe à terminé, met à jour les stocks
                if ($this->statut === 'terminé' && $inventaire->statut !== 'terminé') {
                    $this->finaliserInventaire($inventaire);
                }
                
                $this->dispatch('toast', type: 'success', message: 'Inventaire mis à jour avec succès');
            } else {
                $inventaire = Inventaire::create([
                    'reference' => $this->reference,
                    'motif' => $this->motif,
                    'notes' => $this->notes,
                    'statut' => $this->statut,
                    'user_id' => auth()->user()->id,
                ]);
                
                $this->dispatch('toast', type: 'success', message: 'Inventaire créé avec succès');
                $this->redirectRoute('inventaires.mouvements', ['inventaireId' => $inventaire->id]);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', type: 'error', message: 'Erreur: ' . $e->getMessage());
        }
    }
    
    private function finaliserInventaire($inventaire)
    {
        $mouvements = $inventaire->mouvements()->whereNotNull('quantite_reelle')->get();
        
        foreach ($mouvements as $mouvement) {
            $produit = $mouvement->produit;
            $ecart = $mouvement->quantite_reelle - $mouvement->quantite_theorique;
            $mouvement->update(['ecart' => $ecart]);
            
            if ($ecart != 0) {
                $stockAvant = $produit->stock;
                $stockApres = $mouvement->quantite_reelle;
                
                $produit->update(['stock' => $stockApres]);
                
                HistoriqueStock::create([
                    'produit_id' => $produit->id,
                    'quantite' => $ecart,
                    'stock_avant' => $stockAvant,
                    'stock_apres' => $stockApres,
                    'type_mouvement' => 'inventaire',
                    'user_id' => auth()->id(),
                    'source_id' => $inventaire->id,
                    'source_type' => Inventaire::class,
                    'commentaire' => "Ajustement suite à l'inventaire {$inventaire->reference}"
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.inventaire-form');
    }
}