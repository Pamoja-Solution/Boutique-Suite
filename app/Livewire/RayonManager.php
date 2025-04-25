<?php

namespace App\Livewire;

use App\Models\Rayon;
use App\Models\SousRayon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RayonManager extends Component
{
    use WithPagination;

    // Propriétés pour les rayons
    public $rayon_nom, $rayon_code, $rayon_description, $rayon_icon = 'box', $rayon_ordre, $rayon_id;
    
    // Propriétés pour les sous-rayons
    public $sous_rayon_nom, $sous_rayon_code, $sous_rayon_rayon_id, $sous_rayon_id;
    
    // Gestion de l'interface
    public $isOpen = false;
    public $activeTab = 'rayons';
    public $action = '';
    public $search = '';

    protected $rules = [
        // Règles pour les rayons
        'rayon_nom' => 'required|string|max:255',
        'rayon_code' => 'required|string|max:50|unique:rayons,code',
        'rayon_description' => 'nullable|string',
        'rayon_icon' => 'required|string',
        'rayon_ordre' => 'nullable|integer',
        
        // Règles pour les sous-rayons
        'sous_rayon_nom' => 'required|string|max:255',
        'sous_rayon_code' => 'required|string|max:50|unique:sous_rayons,code_emplacement',
        'sous_rayon_rayon_id' => 'required|exists:rayons,id',
    ];

        public function render()
    {
        return view('livewire.rayon-manager', [
            'rayons' => Rayon::where('nom', 'like', "%{$this->search}%")
                            ->orWhere('code', 'like', "%{$this->search}%")
                            ->paginate(10),
            'sousRayons' => SousRayon::with('rayon') // Charger la relation
                            ->where('nom', 'like', "%{$this->search}%")
                            ->orWhere('code_emplacement', 'like', "%{$this->search}%")
                            ->paginate(10),
            'allRayons' => Rayon::orderBy('nom')->get(),
        ]);
    }

    // Méthodes pour les rayons
    public function createRayon()
    {
        $this->resetRayonInputs();
        $this->action = 'createRayon';
        $this->activeTab = 'rayons';
        $this->openModal();
    }

    public function editRayon($id)
    {
        $rayon = Rayon::findOrFail($id);
        $this->rayon_id = $id;
        $this->rayon_nom = $rayon->nom;
        $this->rayon_code = $rayon->code;
        $this->rayon_description = $rayon->description;
        $this->rayon_icon = $rayon->icon;
        $this->rayon_ordre = $rayon->ordre_affichage;
        
        $this->action = 'editRayon';
        $this->activeTab = 'rayons';
        $this->openModal();
    }

    public function storeRayon()
    {
        $this->validate([
            'rayon_nom' => 'required|string|max:255',
            'rayon_code' => 'required|string|max:50|unique:rayons,code,'.$this->rayon_id,
            'rayon_description' => 'nullable|string',
            'rayon_icon' => 'required|string',
            'rayon_ordre' => 'nullable|integer',
        ]);

        Rayon::updateOrCreate(
            ['id' => $this->rayon_id],
            [
                'nom' => $this->rayon_nom,
                'code' => $this->rayon_code,
                'description' => $this->rayon_description,
                'icon' => $this->rayon_icon,
                'ordre_affichage' => $this->rayon_ordre ?? 0,
            ]
        );

        session()->flash('message', 'Rayon '.($this->rayon_id ? 'mis à jour' : 'créé').' avec succès.');
        $this->closeModal();
        $this->resetRayonInputs();
    }

    public function deleteRayon($id)
    {
        Rayon::find($id)->delete();
        session()->flash('message', 'Rayon supprimé avec succès.');
    }

    // Méthodes pour les sous-rayons
    public function createSousRayon()
    {
        $this->resetSousRayonInputs();
        $this->action = 'createSousRayon';
        $this->activeTab = 'sousRayons';
        $this->openModal();
    }

    public function editSousRayon($id)
    {
        $sousRayon = SousRayon::findOrFail($id);
        $this->sous_rayon_id = $id;
        $this->sous_rayon_nom = $sousRayon->nom;
        $this->sous_rayon_code = $sousRayon->code_emplacement;
        $this->sous_rayon_rayon_id = $sousRayon->rayon_id;
        
        $this->action = 'editSousRayon';
        $this->activeTab = 'sousRayons';
        $this->openModal();
    }

    public function storeSousRayon()
    {
        $this->validate([
            'sous_rayon_nom' => 'required|string|max:255',
            'sous_rayon_code' => 'required|string|max:50|unique:sous_rayons,code_emplacement,'.$this->sous_rayon_id,
            'sous_rayon_rayon_id' => 'required|exists:rayons,id',
        ]);

        SousRayon::updateOrCreate(
            ['id' => $this->sous_rayon_id],
            [
                'nom' => $this->sous_rayon_nom,
                'code_emplacement' => $this->sous_rayon_code,
                'rayon_id' => $this->sous_rayon_rayon_id,
            ]
        );

        session()->flash('message', 'Sous-rayon '.($this->sous_rayon_id ? 'mis à jour' : 'créé').' avec succès.');
        $this->closeModal();
        $this->resetSousRayonInputs();
    }

    public function deleteSousRayon($id)
    {
        SousRayon::find($id)->delete();
        session()->flash('message', 'Sous-rayon supprimé avec succès.');
    }

    // Méthodes communes
    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function resetRayonInputs()
    {
        $this->reset(['rayon_nom', 'rayon_code', 'rayon_description', 'rayon_icon', 'rayon_ordre', 'rayon_id']);
        $this->rayon_icon = 'box';
    }

    public function resetSousRayonInputs()
    {
        $this->reset(['sous_rayon_nom', 'sous_rayon_code', 'sous_rayon_rayon_id', 'sous_rayon_id']);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }
}