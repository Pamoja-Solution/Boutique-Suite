<?php

namespace App\Livewire;

use App\Models\Inventaire;
use Livewire\Component;
use Livewire\WithPagination;

class InventairesList extends Component
{
    use WithPagination;

    public $search = '';
    public $statutFiltre = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statutFiltre' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $inventaires = Inventaire::query()
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('motif', 'like', '%' . $this->search . '%');
            })
            ->when($this->statutFiltre, function ($query) {
                $query->where('statut', $this->statutFiltre);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.inventaires-list', [
            'inventaires' => $inventaires,
        ]);
    }

    public function supprimer($id)
    {
        $inventaire = Inventaire::findOrFail($id);
        
        if ($inventaire->statut === 'terminé') {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer un inventaire terminé');
            return;
        }
        
        $inventaire->delete();
        $this->dispatch('toast', type: 'success', message: 'Inventaire supprimé avec succès');
    }
}