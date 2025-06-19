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
        // Dates de filtrage
        $dateRange = $this->getDateRange();
        
        // Statistiques générales
        $totalProduits = Produit::count();
        $totalClients = Client::count();
        
        // Statistiques filtrées par période
        $totalVentes = Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $totalAchats = Achat::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        // Médicaments à faible stock
        $lowStockProduits = Produit::where('stock', '<', 10)->get();
        
        // Médicaments qui expirent bientôt
        $expiringProduits = Produit::where('date_expiration', '<=', Carbon::now()->addDays(30))->get();
        
        // Ventes par période
        $salesData = $this->getSalesData($dateRange);
        
        // Top 5 des médicaments les plus vendus
        $topSellingProduits = $this->getTopSellingProduits($dateRange);
        
        // Statistiques financières
        $financialStats = $this->getFinancialStats($dateRange);
        
        // Statistiques des vendeurs pour aujourd'hui
        $todaySalesBySeller = $this->getTodaySalesBySeller();
        
        return view('livewire.dashboard', [
            'totalProduits' => $totalProduits,
            'totalClients' => $totalClients,
            'totalVentes' => $totalVentes,
            'totalAchats' => $totalAchats,
            'lowStockProduits' => $lowStockProduits,
            'expiringProduits' => $expiringProduits,
            'salesData' => $salesData,
            'topSellingProduits' => $topSellingProduits,
            'totalRevenue' => $financialStats['totalRevenue'],
            'totalCost' => $financialStats['totalCost'],
            'profit' => $financialStats['profit'],
            'period' => $this->period,
            'todaySalesBySeller' => $todaySalesBySeller // Ajout des stats des vendeurs
        ]);
    }

    private function getTodaySalesBySeller()
    {
        $today = Carbon::today();
        $endOfDay = Carbon::today()->endOfDay();
        
        return User::whereHas('ventes', function($query) use ($today, $endOfDay) {
                $query->whereBetween('created_at', [$today, $endOfDay]);
            })
            ->withCount(['ventes as sales_count' => function($query) use ($today, $endOfDay) {
                $query->whereBetween('created_at', [$today, $endOfDay]);
            }])
            ->withSum(['ventes as sales_amount' => function($query) use ($today, $endOfDay) {
                $query->whereBetween('created_at', [$today, $endOfDay]);
            }], 'total')
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'sales_count' => $user->sales_count,
                    'sales_amount' => $user->sales_amount
                ];
            });
    }
    
    public function changePeriod($period)
    {
        $this->period = $period;
    }
    
    private function getDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->period) {
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }
    
    private function getSalesData($dateRange)
    {
        if (config('database.default') === 'sqlite') {
            // Version pour SQLite
            if ($this->period === 'year') {
                return Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->selectRaw("strftime('%Y-%m', created_at) as date, SUM(total) as amount")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            } else {
                return Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->selectRaw("date(created_at) as date, SUM(total) as amount")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            }
        } else {
            // Version pour MySQL/MariaDB
            $groupByFormat = $this->period === 'year' ? '%Y-%m' : '%Y-%m-%d';
            
            return Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->selectRaw("DATE_FORMAT(created_at, '{$groupByFormat}') as date, SUM(total) as amount")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }
    }
    
    private function getTopSellingProduits($dateRange)
    {
        return DB::table('details_vente')
            ->join('produits', 'details_vente.produit_id', '=', 'produits.id')
            ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->whereBetween('ventes.created_at', [$dateRange['start'], $dateRange['end']])
            ->select('produits.nom', DB::raw('SUM(details_vente.quantite) as total_sold'))
            ->groupBy('produits.nom')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function getFinancialStats($dateRange)
{
    // Détail du bénéfice par produit
    $productsProfit = DB::table('details_vente')
        ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
        ->join('produits', 'details_vente.produit_id', '=', 'produits.id')
        ->whereBetween('ventes.created_at', [$dateRange['start'], $dateRange['end']])
        ->select(
            'produits.nom',
            DB::raw('SUM(details_vente.quantite) as total_quantity'),
            DB::raw('SUM(details_vente.quantite * details_vente.prix_unitaire) as product_revenue'),
            DB::raw('SUM(details_vente.quantite * produits.prix_achat) as product_cost'),
            DB::raw('SUM(details_vente.quantite * (details_vente.prix_unitaire - produits.prix_achat)) as product_profit')
        )
        ->groupBy('produits.nom')
        ->get();
//dd( $productsProfit);
    $totalRevenue = $productsProfit->sum('product_revenue');
    $totalCost = $productsProfit->sum('product_cost');
    $totalProfit = $productsProfit->sum('product_profit');

    return [
        'totalRevenue' => $totalRevenue,
        'totalCost' => $totalCost,
        'profit' => $totalProfit,
        'margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
        'productsProfit' => $productsProfit // Optionnel: pour afficher le détail par produit
    ];
}
}