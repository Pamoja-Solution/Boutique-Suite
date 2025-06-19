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

class FuturistSalesDashboard extends Component
{
    use WithPagination;

    public $showFilters = false;
    public $showDetailsModal = false;
    public $showExportModal = false;
    public $selectedSale = null;
    public $name = 'details-modal'; // Donnez un nom unique à votre modal

    // Filtres
    public $startDate;
    public $endDate;
    public $selectedClient;
    public $search = '';
    public $perPage = 10;
    public $statusFilter = 'all';
    
    // Options d'export
    public $exportFormat = 'excel';
    public $exportStartDate;
    public $exportEndDate;
    
    // Tri
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    

    // Propriétés

public $includeCharts = true;
public $includeDetails = true;
public $pdfOrientation = 'portrait';

    // Données pour les graphiques
    public $salesChartData = [
        'labels' => [],
        'data' => []
    ];
    
    // Statistiques
    public $statsPeriod = '30d'; // 7d, 30d, 90d, custom
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        $this->exportStartDate = $this->startDate;
        $this->exportEndDate = $this->endDate;
        
        $this->loadChartData();
        $this->dispatch('updateSalesChart', data: $this->salesChartData);
    }

    public function loadChartData()
    {
        $salesData = Vente::query()
            ->forCurrentUser()
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->salesChartData = [
            'labels' => $salesData->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            })->toArray(),
            'data' => $salesData->pluck('total')->toArray()
        ];

        $this->dispatch('updateSalesChart', data: $this->salesChartData);
    }

    public function updatedStatsPeriod($value)
    {
        switch ($value) {
            case '7d':
                $this->startDate = Carbon::today()->subDays(7)->format('Y-m-d');
                break;
            case '30d':
                $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
                break;
            case '90d':
                $this->startDate = Carbon::today()->subDays(90)->format('Y-m-d');
                break;
            case 'custom':
                // L'utilisateur définira manuellement les dates
                break;
        }
        
        $this->loadChartData();
    }

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
        $this->selectedSale = Vente::forCurrentUser()
            ->with(['client', 'details.produit'])
            ->find($saleId);
        $this->showDetailsModal = true;
    }

    public function hideDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedSale = null;
    }

    public function printInvoice($saleId)
    {
        $vente = Vente::forCurrentUser()
            ->with(['client', 'details.produit'])
            ->findOrFail($saleId);
            
        $logoPath = config('app.logo');
        $entreprise = [
            'nom' => config('app.name'),
            'adresse' => config('app.adresse', '123 Rue du Commerce'),
            'telephone' => config('app.telephone', '+1234567890'),
            'email' => config('app.email', 'contact@example.com'),
            'site_web' => config('app.url'),
            'logo' => public_path($logoPath)
        ];
        
        
        return to_route("ventes.print-invoice",['vente'=>$vente]);
        
    }

    public function sendEmail($saleId)
    {
        $vente = Vente::forCurrentUser()->findOrFail($saleId);
        // Ici vous ajouteriez la logique d'envoi d'email
        
        $this->dispatch('notify', 
            type: 'success',
            message: 'Facture #' . $saleId . ' envoyée par email avec succès'
        );
    }

    public function exportToExcel()
    {
        $this->validate([
            'exportStartDate' => 'required|date',
            'exportEndDate' => 'required|date|after_or_equal:exportStartDate',
        ]);

        $this->showExportModal = false;
        
        return Excel::download(
            new VentesExport($this->exportStartDate, $this->exportEndDate, $this->statusFilter), 
            'ventes_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function resetFilters()
{
    $this->reset([
        'search', 
        'selectedClient', 
        'statusFilter'
    ]);
    
    // Réinitialisation robuste des dates
    $this->endDate = Carbon::today()->format('Y-m-d');
    $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
    $this->statsPeriod = '30d';
    
    $this->loadChartData();
}

    public function updatedStartDate()
    {
        $this->statsPeriod = 'custom';
        $this->loadChartData();
    }

    public function updatedEndDate()
    {
        $this->loadChartData();
    }

    public function updatedStatusFilter()
    {
        $this->loadChartData();
    }

    // Propriétés calculées
    public function getTotalSalesProperty()
    {
        return Vente::query()
            ->forCurrentUser()
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->sum('total');
    }

    public function getSalesCountProperty()
    {
        return Vente::query()
            ->forCurrentUser()
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->whereBetween('created_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->count();
    }

    public function getProduitsSoldProperty()
    {
        return DB::table('details_vente')
            ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
            ->where('ventes.user_id', Auth::user()->id)
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('ventes.statut', $this->statusFilter);
            })
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
    try {
        $currentPeriod = Vente::query()
            ->forCurrentUser()
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->sum('total');
        
        // Calcul plus robuste de la période précédente
        $daysDiff = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
        $previousStart = Carbon::parse($this->startDate)->subDays($daysDiff)->startOfDay();
        $previousEnd = Carbon::parse($this->startDate)->subDay()->endOfDay();

        $previousPeriod = Vente::query()
            ->forCurrentUser()
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total');
            
        return $previousPeriod != 0 
            ? round(($currentPeriod - $previousPeriod) / $previousPeriod * 100, 2) 
            : 0;
    } catch (\Exception $e) {
        return 0; // Retourne 0 en cas d'erreur de calcul
    }
}

    public function getClientsProperty()
    {
        return Client::orderBy('nom')->get();
    }

    public function getTopProduitsProperty()
{
    // Sauvegarder la configuration actuelle
    $strictMode = DB::getConfig('strict');
    
    // Désactiver temporairement le mode strict
    config(['database.connections.mysql.strict' => false]);
    DB::purge();
    
    $results = Produit::query()
        ->select('produits.*', DB::raw('SUM(details_vente.quantite) as total_quantity'))
        ->join('details_vente', 'details_vente.produit_id', '=', 'produits.id')
        ->join('ventes', 'details_vente.vente_id', '=', 'ventes.id')
        ->where('ventes.user_id', Auth::user()->id)
        ->when($this->statusFilter !== 'all', function ($query) {
            $query->where('ventes.statut', $this->statusFilter);
        })
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

 // Méthode d'export
    public function exportData()
    {
        $this->validate([
            'exportStartDate' => 'required|date',
            'exportEndDate' => 'required|date|after_or_equal:exportStartDate',
        ]);

        if ($this->exportFormat === 'excel') {
            return $this->exportToExcel();
        } elseif ($this->exportFormat === 'pdf') {
            return $this->exportToPdf();
        }
    }

    // Méthode pour exporter en PDF
    public function exportToPdf()
    {
        $sales = Vente::with(['client', 'details.produit'])
            ->whereBetween('created_at', [$this->exportStartDate, $this->exportEndDate])
            ->get();

        $pdf = PDF::loadView('exports.sales-pdf', [
            'sales' => $sales,
            'startDate' => $this->exportStartDate,
            'endDate' => $this->exportEndDate,
            'includeCharts' => $this->includeCharts,
            'includeDetails' => $this->includeDetails,
            'orientation' => $this->pdfOrientation,
        ]);
        $pdf->setPaper('A4', $this->pdfOrientation); // $this->pdfOrientation = 'portrait' ou 'landscape'
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "ventes-{$this->exportStartDate}-a-{$this->exportEndDate}.pdf"
        );
    }
    public function getSalesProperty()
    {
        return Vente::query()
            ->forCurrentUser()
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
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('client', function ($q) {
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('id', 'like', '%'.$this->search.'%')
                    ->orWhere('reference', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.futurist-sales-dashboard', [
            'sales' => $this->sales,
            'clients' => $this->clients,
            'topProduits' => $this->topProduits,
           // 'salesByStatus' => $this->salesByStatus,
        ]);
    }
}