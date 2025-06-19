<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vente;
use App\Models\Client;
use App\Models\Produit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\VentesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class LesVentes extends Component
{
    use WithPagination;

    public $showFilters = false;
    public $showDetailsModal = false;
    public $selectedSale = null;
    
    // Filtres
    public $startDate;
    public $endDate;
    public $selectedClient;
    public $search = '';
    public $perPage = 10;
    
    // Tri
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Données pour les graphiques
    public $salesChartData = [
        'labels' => [],
        'data' => []
    ];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        
        $this->loadChartData();
        $this->dispatch('updateSalesChart', data: $this->salesChartData);
    }

 // ...

public function loadChartData()
{
    $salesData = Vente::query()
        ->selectRaw('DATE(created_at) as date, SUM(total) as total')
                    ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])

        ->groupBy('date')
        ->orderBy('date')
        ->get();
    $this->salesChartData = [
        'labels' => $salesData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->translatedFormat('d M');
        })->toArray(),
        'data' => $salesData->pluck('total')->toArray(),
        'colors' => ['rgba(99, 102, 241, 0.2)', 'rgba(99, 102, 241, 1)'] // Couleurs DaisyUI
    ];


    $this->dispatch('updateSalesChart', data: $this->salesChartData);
}

// ...


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function showDetails($saleId)
    {
        $this->selectedSale = Vente::with(['client', 'details.produit'])->find($saleId);
        $this->showDetailsModal = true;
    }

    public function hideDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedSale = null;
    }

    public function printInvoice($saleId)
    {
        $vente = Vente::with(['client', 'details.produit'])->findOrFail($saleId);

        $logoPath = config('app.logo');
        $entreprise=[
                    'nom' => config('app.name'),
                    'adresse' => config('app.adresse', '123 Rue du Commerce'),
                    'telephone' => config('app.telephone', '+1234567890'),
                    'email' => config('app.email', 'contact@example.com'),
                    'site_web' => config('app.url'),
                    'logo' => (public_path($logoPath)) 
        ];
        

        $pdf = Pdf::loadView('pdf.invoice2', compact('vente','entreprise'))
                ->setPaper([0, 0, 226.77, 425.19]); // 80mm x 150mm en points (1mm ≈ 2.83 points)

        return response()->streamDownload(
            fn () => print($pdf->output()), 
            "facture_{$saleId}.pdf"
        );
    }

    public function sendEmail($saleId)
    {
        // Logique pour envoyer la facture par email
        $this->dispatch('notify', message: 'Facture #' . $saleId . ' envoyée par email');
    }

    public function exportToExcel()
    {
        // Logique pour exporter vers Excel
        $this->dispatch('notify', message: 'Exportation vers Excel en cours...');
        return Excel::download(new VentesExport, 'ventes.xlsx');
    }

    public function resetFilters()
    {
        $this->reset(['selectedClient', 'search']);
        $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->loadChartData();
    }

    public function updatedStartDate()
    {
        $this->loadChartData();
    }

    public function updatedEndDate()
    {
        $this->loadChartData();
    }

    public function getTotalSalesProperty()
    {
        return Vente::query()
            ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->where('user_id', Auth::user()->id)
            ->sum('total');
    }

    public function getSalesCountProperty()
    {
        return Vente::query()
            ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->count();
    }

    public function getProduitsSoldProperty()
    {
        return DB::table('details_vente')
            ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->whereBetween('ventes.created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->sum('quantite');
    }

    public function getAverageCartProperty()
    {
        $count = $this->salesCount;
        return $count > 0 ? $this->totalSales / $count : 0;
    }

    public function getSalesTrendProperty()
    {
        $currentPeriod = Vente::query()
            ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->sum('total');
            
        $previousPeriod = Vente::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->subDays(Carbon::parse($this->startDate)->diffInDays($this->endDate))->startOfDay(),
                Carbon::parse($this->startDate)->subDay()->endOfDay()
            ])
            ->sum('total');
            
        return $previousPeriod != 0 ? round(($currentPeriod - $previousPeriod) / $previousPeriod * 100, 2) : 0;
    }

    public function getSalesCountTrendProperty()
{
    // Si les dates ne sont pas définies, retourner 0
    if (empty($this->startDate) || empty($this->endDate)) {
        return 0;
    }

    try {
        $currentStart = Carbon::parse($this->startDate);
        $currentEnd = Carbon::parse($this->endDate)->endOfDay();
        
        // Calculer la différence en jours entre les dates
        $daysDiff = $currentStart->diffInDays($currentEnd);

        // Période actuelle
        $currentPeriod = Vente::query()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();
            
        // Période précédente (même durée que la période actuelle)
        $previousStart = $currentStart->copy()->subDays($daysDiff + 1);
        $previousEnd = $currentStart->copy()->subDay()->endOfDay();

        $previousPeriod = Vente::query()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();
            
        // Calcul du pourcentage de changement
        if ($previousPeriod > 0) {
            return round(($currentPeriod - $previousPeriod) / $previousPeriod * 100, 2);
        } else {
            // Si pas de ventes précédentes mais des ventes actuelles, retourner +100%
            return $currentPeriod > 0 ? 100 : 0;
        }
    } catch (\Exception $e) {
        // En cas d'erreur (par exemple format de date invalide), retourner 0
        return 0;
    }
}
    public function getProduitsTrendProperty()
    {
        $currentPeriod = DB::table('details_vente')
            ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->whereBetween('ventes.created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->sum('quantite');
            
        $previousPeriod = DB::table('details_vente')
            ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->whereBetween('ventes.created_at', [
                Carbon::parse($this->startDate)->subDays(Carbon::parse($this->startDate)->diffInDays($this->endDate))->startOfDay(),
                Carbon::parse($this->startDate)->subDay()->endOfDay()
            ])
            ->sum('quantite');
            
        return $previousPeriod != 0 ? round(($currentPeriod - $previousPeriod) / $previousPeriod * 100, 2) : 0;
    }

    public function getAverageCartTrendProperty()
    {
        $currentAvg = $this->averageCart;
        
        $previousPeriodSales = Vente::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->subDays(Carbon::parse($this->startDate)->diffInDays($this->endDate))->startOfDay(),
                Carbon::parse($this->startDate)->subDay()->endOfDay()
            ])
            ->get();
            
        $previousAvg = $previousPeriodSales->count() > 0 
            ? $previousPeriodSales->sum('total') / $previousPeriodSales->count() 
            : 0;
            
        return $previousAvg != 0 ? round(($currentAvg - $previousAvg) / $previousAvg * 100, 2) : 0;
    }

    public function getClientsProperty()
    {
        return Client::orderBy('nom')->get();
    }

    public function getTopProduitsProperty()
{
    // Sauvegarder la configuration actuelle
    $strictMode = config('database.connections.mysql.strict');
    
    // Désactiver temporairement le mode strict
    config(['database.connections.mysql.strict' => false]);
    DB::purge();
    
    $results = Produit::query()
        ->select('produits.*', DB::raw('SUM(details_vente.quantite) as total_quantity'))
        ->join('details_vente', 'details_vente.produit_id', '=', 'produits.id')
        ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
        ->whereBetween('ventes.created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
        ->groupBy('produits.id')
        ->orderByDesc('total_quantity')
        ->limit(5)
        ->get();
    
    // Restaurer la configuration originale
    config(['database.connections.mysql.strict' => $strictMode]);
    DB::purge();
    
    return $results;
}

    public function getSalesProperty()
    {
        return Vente::query()
            ->with(['client'])
            ->when($this->startDate, function ($query) {
                $query->where('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('created_at', '<=', Carbon::parse($this->endDate)->endOfDay());
            })
            ->when($this->selectedClient, function ($query) {
                $query->where('client_id', $this->selectedClient);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('client', function ($q) {
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('id', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.les-ventes',[
            'sales' => $this->sales,
            'clients' => $this->clients,
            'topProduits' => $this->topProduits,
        ]);
    }
}