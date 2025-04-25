<?php

namespace App\Livewire;

use App\Models\Produit;
use App\Models\Vente;
use App\Models\Achat;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $period = 'month';
    
    public function render()
    {
        // Statistiques générales
        $totalProduits = Produit::count();
        $totalClients = Client::count();
        $totalVentes = Vente::count();
        $totalAchats = Achat::count();
        
        // Médicaments à faible stock (moins de 10 unités)
        $lowStockProduits = Produit::where('stock', '<', 10)->get();
        
        // Médicaments qui expirent bientôt (dans les 30 jours)
        $expiringProduits = Produit::where('date_expiration', '<=', Carbon::now()->addDays(30))->get();
        
        // Ventes par période
        $salesData = $this->getSalesData();
        
        // Top 5 des médicaments les plus vendus
        $topSellingProduits = DB::table('details_vente')
            ->join('produits', 'details_vente.produit_id', '=', 'produits.id')
            ->select('produits.nom', DB::raw('SUM(details_vente.quantite) as total_sold'))
            ->groupBy('produits.nom')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Statistiques financières
        $totalRevenue = Vente::sum('total');
        $totalCost = Achat::sum('total');
        $profit = $totalRevenue - $totalCost;
        
        return view('livewire.dashboard', [
            'totalProduits' => $totalProduits,
            'totalClients' => $totalClients,
            'totalVentes' => $totalVentes,
            'totalAchats' => $totalAchats,
            'lowStockProduits' => $lowStockProduits,
            'expiringProduits' => $expiringProduits,
            'salesData' => $salesData,
            'topSellingProduits' => $topSellingProduits,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'profit' => $profit
        ]);
    }
    
    public function changePeriod($period)
    {
        $this->period = $period;
    }
    
    private function getSalesData()
    {
        switch ($this->period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                $groupBy = 'day';
                $dateFormat = '%Y-%m-%d';
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                $groupBy = 'day';
                $dateFormat = '%Y-%m-%d';
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                $groupBy = 'month';
                $dateFormat = '%Y-%m';
                break;
            default:
                $startDate = Carbon::now()->subMonth();
                $groupBy = 'day';
                $dateFormat = '%Y-%m-%d';
        }
        
        /*$salesData = Vente::where('created_at', '>=', $startDate)
        ->select(DB::raw("strftime('%Y-%m-%d', created_at) as date"), DB::raw('SUM(total) as amount'))
        ->groupBy('date')
        ->orderBy('date')
        ->get();*/
        $salesData = Vente::where('created_at', '>=', $startDate)
        ->selectRaw("DATE(created_at) as date, SUM(total) as amount")
        ->groupByRaw("DATE(created_at)")
        ->orderBy('date')
        ->get();
            
        return $salesData;
    }
}