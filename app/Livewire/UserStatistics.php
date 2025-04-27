<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Expense;
use App\Models\Produit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserStatistics extends Component
{
    use WithPagination;
    
    public $selectedUser = null;
    public $dateRange = 'today';
    public $customStartDate;
    public $customEndDate;
    public $searchQuery = '';
    
    public function mount()
    {
        $this->customStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->customEndDate = Carbon::now()->format('Y-m-d');
    }
    
    public function selectUser($userId)
    {
        $this->selectedUser = $userId;
    }
    
    public function resetUserSelection()
    {
        $this->selectedUser = null;
    }
    
    public function getDateFilter()
    {
        return match($this->dateRange) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom' => [
                Carbon::parse($this->customStartDate)->startOfDay(), 
                Carbon::parse($this->customEndDate)->endOfDay()
            ],
            default => [Carbon::today(), Carbon::today()->endOfDay()]
        };
    }
    
    public function getUsersProperty()
    {
        return User::where('name', 'like', "%{$this->searchQuery}%")
            ->orWhere('email', 'like', "%{$this->searchQuery}%")
            ->orWhere('matricule', 'like', "%{$this->searchQuery}%")
            ->orderBy('name')
            ->paginate(10);
    }
    
    public function getUserStatsProperty()
    {
        if (!$this->selectedUser) {
            return null;
        }
        
        [$startDate, $endDate] = $this->getDateFilter();
        
        $user = User::findOrFail($this->selectedUser);
        
        // Statistiques de ventes
        $ventesQuery = Vente::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalVentes = $ventesQuery->count();
        $montantTotalVentes = $ventesQuery->sum('total');
        
        // Moyenne des ventes par jour
        $numberOfDays = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $moyenneVentesParJour = $totalVentes / $numberOfDays;
        $moyenneMontantParJour = $montantTotalVentes / $numberOfDays;
        
        // Produits les plus vendus par cet utilisateur
        $topProduits = DetailVente::join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->join('produits', 'details_vente.produit_id', '=', 'produits.id')
            ->where('ventes.user_id', $user->id)
            ->whereBetween('ventes.created_at', [$startDate, $endDate])
            ->select(
                'produits.id',
                'produits.nom',
                'produits.reference_interne',
                DB::raw('SUM(details_vente.quantite) as total_quantity'),
                DB::raw('SUM(details_vente.quantite * details_vente.prix_unitaire) as total_amount')
            )
            ->groupBy('produits.id', 'produits.nom', 'produits.reference_interne')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        // Dépenses effectuées par l'utilisateur
        $depenses = Expense::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalDepenses = $depenses->where('type', 'expense')->sum('amount');
        $totalRevenus = $depenses->where('type', 'income')->sum('amount');
        
        // Évolution des ventes sur la période
        $evolutionVentes = Vente::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                return $item;
            });
        
        return [
            'user' => $user,
            'periode' => [
                'debut' => $startDate->format('d/m/Y'),
                'fin' => $endDate->format('d/m/Y'),
            ],
            'ventes' => [
                'total' => $totalVentes,
                'montant' => $montantTotalVentes,
                'moyenne_par_jour' => $moyenneVentesParJour,
                'montant_moyen_par_jour' => $moyenneMontantParJour,
                'evolution' => $evolutionVentes,
            ],
            'produits' => [
                'top' => $topProduits,
            ],
            'finances' => [
                'depenses' => $totalDepenses,
                'revenus' => $totalRevenus,
                'balance' => $totalRevenus - $totalDepenses,
            ],
        ];
    }
    
    public function render()
    {
        return view('livewire.user-statistics', [
            'users' => $this->users,
            'userStats' => $this->userStats,
        ]);
    }
}