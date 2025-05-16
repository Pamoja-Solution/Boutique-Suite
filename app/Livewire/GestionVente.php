<?php
namespace App\Livewire;

use App\Models\Produit;
use App\Models\Client;
use App\Models\Vente;
use App\Models\DetailVente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionVente extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedProduits = [];
    public $quantities = [];
    public $clientId = null;
    public $clients = [];
    
    // Modal states
    public $showModal = false;
    public $modalType = ''; // 'new-sale', 'view-stock', etc.
    
    // Produit sélectionné pour voir les détails
    public $selectedProduit = null;
    
    // Nouvelles propriétés pour la recherche de clients
    public $clientSearch = '';
    public $filteredClients = [];
    
    // Propriétés pour le nouveau client
    public $newClient = [
        'nom' => '',
        'telephone' => '',
        'email' => '',
        'adresse' => ''
    ];

    // Propriété pour suivre les erreurs de validation
    public $hasValidationErrors = false;

    // Modifiez les règles de validation
    protected $rules = [
        'clientId' => 'required_without:newClient.nom|exists:clients,id',
        'selectedProduits' => 'required|array|min:1',
        'selectedProduits.*' => 'exists:produits,id',
        'quantities.*' => 'required|integer|min:1',
        'newClient.nom' => 'required_if:clientId,null|string|max:255',
        'newClient.telephone' => 'required_if:clientId,null|string|max:20',
        'newClient.email' => 'nullable|email|max:255',
        'newClient.adresse' => 'nullable|string|max:255',
    ];

    // Modifiez la méthode validateSaleForm
    public function validateSaleForm()
    {
        try {
            // Valider soit clientId, soit newClient
            if (empty($this->clientId)) {
                $this->validate([
                    'newClient.nom' => 'required|string|max:255',
                    'newClient.telephone' => 'required|string|max:20',
                    'newClient.email' => 'nullable|email|max:255',
                    'newClient.adresse' => 'nullable|string|max:255',
                ]);
            } else {
                $this->validate(['clientId' => 'exists:clients,id']);
            }

            // Valider les produits
            $this->validate([
                'selectedProduits' => 'required|array|min:1',
                'selectedProduits.*' => 'exists:produits,id',
                'quantities.*' => 'required|integer|min:1',
            ]);

            $this->hasValidationErrors = false;
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->hasValidationErrors = true;
            return false;
        }
    }
    
    public $venteIdPourImpression = null;

    public function confirmSale()
    {
        if (!$this->validateSaleForm()) {
            return;
        }
    
        // Vérifier le stock
        $produits = Produit::whereIn('id', $this->selectedProduits)->get();
        foreach ($produits as $prod) {
            if (($this->quantities[$prod->id] ?? 0) > $prod->stock) {
                $this->addError('quantities.' . $prod->id, 'Stock insuffisant');
                return;
            }
        }
    
        try {
            DB::beginTransaction();
    
            // Créer un nouveau client si nécessaire
            if (empty($this->clientId)) {
                $client = Client::create([
                    'nom' => $this->newClient['nom'],
                    'telephone' => $this->newClient['telephone'],
                    'email' => $this->newClient['email'] ?? null,
                    'adresse' => $this->newClient['adresse'] ?? null,
                ]);
                $this->clientId = $client->id;
            }
    
            // Calcul du total
            $total = 0;
            foreach ($produits as $prod) {
                $quantity = $this->quantities[$prod->id] ?? 0;
                $total += $prod->prix_vente * $quantity;
            }
    
            $vente = Vente::create([
                'client_id' => $this->clientId,
                'total' => $total,
                'user_id' => Auth::user()->id,
            ]);
    
            foreach ($produits as $prod) {
                $quantity = $this->quantities[$prod->id] ?? 0;
                
                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $prod->id,
                    'quantite' => $quantity,
                    'prix_unitaire' => $prod->prix_vente
                ]);
                
                $prod->stock -= $quantity;
                $prod->save();
            }
    
            DB::commit();
    
            session()->flash('message', 'Vente effectuée avec succès');
            
            $this->reset(['selectedProduits', 'quantities', 'newClient']);
            
            // Rediriger vers la route d'impression immédiatement
            $this->dispatch('openNewTab', url: route('ventes.print-invoice', [
                'vente' => $vente->id,
                
            
            ]));
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
    
    // Méthode modifiée pour rechercher les clients manuellement
    public function searchClients()
    {
        $client = $this->filteredClients = Client::when($this->clientSearch, function($query) {
            return $query->where('nom', 'like', '%'.$this->clientSearch.'%')
                    ->orWhere('telephone', 'like', '%'.$this->clientSearch.'%');
        })
        ->limit(10)
        ->get();
        return $client;
    }

    protected $messages = [
        'clientId.required' => 'Veuillez sélectionner un client',
        'selectedProduits.required' => 'Veuillez sélectionner au moins un produit',
        'quantities.*.required' => 'La quantité est requise',
        'quantities.*.min' => 'La quantité doit être au moins 1',
        // Messages pour le nouveau client
        'newClient.nom.required' => 'Le nom est requis',
        'newClient.telephone.required' => 'Le téléphone est requis',
        'newClient.email.email' => 'Format d\'email invalide',
    ];

    // Propriétés pour le suivi des modifications
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->clients = Client::all();
        $this->updateFilteredClients();
    }
    
    // Nouvelle méthode pour rechercher les produits (action manuelle)
    public function searchProduits()
    {
        $this->resetPage(); // Réinitialiser la pagination
    }
    
    public function render()
    {
        $query = Produit::query()
        ->where(function ($query) {
            $query->where('nom', 'like', '%' . $this->search . '%')
                ->orWhere('reference_interne', 'like', '%' . $this->search . '%');
        })
        ->where('stock', '>', 0)
        ->where(function ($query) {
            $query->whereNull('date_expiration')
                ->orWhere('date_expiration', '>', now());
        });

    // Toujours inclure les produits déjà sélectionnés même s'ils ne correspondent pas à la recherche
    if (!empty($this->selectedProduits)) {
        $query->orWhereIn('id', $this->selectedProduits);
    }

    $produits = $query->paginate(10);
    
            
        $produits = $query->paginate(10);
        
        $panier = [];
        $total = 0;
        
        if (!empty($this->selectedProduits)) {
            $panier = Produit::whereIn('id', $this->selectedProduits)->get();
            
            foreach ($panier as $prod) {
                $quantity = $this->quantities[$prod->id] ?? 0;
                $total += $prod->prix_vente * $quantity;
            }
        }
        
        return view('livewire.gestion-vente', [
            'produits' => $produits,
            'panier' => $panier,
            'total' => $total,
            "client" => $this->searchClients(),
        ]);
    }
    
    public function addToCart($produitId)
{
    // Vérifier si le produit existe dans la base
    $produit = Produit::find($produitId);
    
    if (!$produit) {
        return;
    }

    // Ajouter seulement si pas déjà dans le panier
    if (!in_array($produitId, $this->selectedProduits)) {
        $this->selectedProduits[] = $produitId;
        $this->quantities[$produitId] = 1;
    }
    
    // Forcer le rafraîchissement sans réinitialiser la pagination
    $this->dispatch('cartUpdated');
}
    
    public function removeFromCart($produitId)
    {
        $index = array_search($produitId, $this->selectedProduits);
        
        if ($index !== false) {
            unset($this->selectedProduits[$index]);
            unset($this->quantities[$produitId]);
            $this->selectedProduits = array_values($this->selectedProduits);
        }
    }
    
 
    // Fonction pour incrémenter directement la quantité

        public function incrementQuantity($produitId)
    {
        $currentQty = $this->quantities[$produitId] ?? 1;
        $this->updateQuantity($produitId, $currentQty + 1);
    }

    public function decrementQuantity($produitId)
    {
        $currentQty = $this->quantities[$produitId] ?? 1;
        if ($currentQty > 1) {
            $this->updateQuantity($produitId, $currentQty - 1);
        }
    }

    public function updateQuantity($produitId, $quantity)
    {
        $produit = Produit::find($produitId);
        
        if ($produit && $quantity > 0 && $quantity <= $produit->stock) {
            $this->quantities[$produitId] = $quantity;
        } elseif ($quantity <= 0) {
            $this->quantities[$produitId] = 1;
        } elseif ($quantity > $produit->stock) {
            $this->quantities[$produitId] = $produit->stock;
            $this->addError('quantities.' . $produitId, 'Stock insuffisant');
        }
    }
   
    
    public function updateFilteredClients()
    {
        if (empty($this->clientSearch)) {
            $this->filteredClients = Client::take(10)->get();
        } else {
            $this->filteredClients = Client::where('nom', 'like', '%' . $this->clientSearch . '%')
                ->orWhere('adresse', 'like', '%' . $this->clientSearch . '%')
                ->orWhere('telephone', 'like', '%' . $this->clientSearch . '%')
                ->take(10)
                ->get();
        }
    }
    
    public function selectClient($id)
    {
        $this->clientId = $id;
        $this->clientSearch = Client::find($id)->nom; // AJOUTER: afficher le nom du client sélectionné
        $this->resetErrorBag('clientId');
    }
    
    public function openNewClientModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->newClient = [
            'nom' => '',
            'telephone' => '',
            'email' => '',
            'adresse' => ''
        ];
        $this->modalType = 'new-client';
        $this->showModal = true;
    }
    
    public function createClient()
    {
        $this->validate([
            'newClient.nom' => 'required|string|max:255',
            'newClient.telephone' => 'required|string|max:20',
            'newClient.email' => 'nullable|email|max:255',
            'newClient.adresse' => 'nullable|string|max:255',
        ]);
        
        $client = Client::create([
            'nom' => $this->newClient['nom'],
            'telephone' => $this->newClient['telephone'],
            'email' => $this->newClient['email'] ?? null,
            'adresse' => $this->newClient['adresse'] ?? null,
        ]);
        
        $this->clientId = $client->id;
        $this->clients = Client::all();
        $this->showModal = false;
        
        session()->flash('message', 'Client créé avec succès');
    }
    
    // Méthodes existantes pour modal, etc.
    public function openModal($type, $produitId = null)
    {
        $this->modalType = $type;
        
        if ($type === 'details' && $produitId) {
            $this->selectedProduit = Produit::find($produitId);
        }
        
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduit = null;
    }
    
    public function getRecentVentes()
    {
        return Vente::with('client', 'detailsVentes.produit')
        ->forCurrentUser()  // Utilisation du scope
        ->today()          // Utilisation du scope
        ->latest()
        ->get();
    }
    
    public function getProduitsExpiration()
    {
        $oneMonthFromNow = now()->addMonth();
        
        return Produit::whereNotNull('date_expiration')
            ->where('date_expiration', '<=', $oneMonthFromNow)
            ->where('date_expiration', '>', now())
            ->where('stock', '>', 0)
            ->orderBy('date_expiration')
            ->get();
    }
    
    public function getProduitsLowStock()
    {
        return Produit::where('stock', '<', DB::raw('seuil_alerte'))
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->get();
    }
    
    // Méthode pour déboguer les erreurs de validation
    public function getErrorsProperty()
    {
        return $this->getErrorBag()->toArray();
    }
}