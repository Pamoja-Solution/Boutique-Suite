<?php

namespace App\Livewire;

use App\Models\Produit;
use App\Models\Vente;
use App\Models\Achat;
use App\Models\Client;
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
        $totalProduits = Produit::count(); // Non affecté par la période
        $totalClients = Client::count();   // Non affecté par la période
        
        // Statistiques filtrées par période
        $totalVentes = Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $totalAchats = Achat::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        // Médicaments à faible stock (moins de 10 unités) - Non affecté par la période
        $lowStockProduits = Produit::where('stock', '<', 10)->get();
        
        // Médicaments qui expirent bientôt (dans les 30 jours) - Non affecté par la période
        $expiringProduits = Produit::where('date_expiration', '<=', Carbon::now()->addDays(30))->get();
        
        // Ventes par période
        $salesData = $this->getSalesData($dateRange);
        
        // Top 5 des médicaments les plus vendus (filtré par période)
        $topSellingProduits = $this->getTopSellingProduits($dateRange);
        
        // Statistiques financières (filtrées par période)
        $financialStats = $this->getFinancialStats($dateRange);
        
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
            'period' => $this->period
        ]);
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
        $totalRevenue = Vente::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->sum('total');
        $totalCost = Achat::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->sum('total');
        
        return [
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'profit' => $totalRevenue - $totalCost
        ];
    }
}