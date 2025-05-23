<?php

namespace App\Livewire;

use App\Exports\SalesExport;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]

class SalesReport extends Component
{
    use WithPagination;

    #[Url]
    public $reportType = 'daily';

    #[Url]
    public $selectedUserId = 'all';

    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    public $showExportModal = false;
    public $exportType = 'pdf';
    public $pdfOrientation = 'portrait';
    public $includeCharts = true;
    public $includeDetails = true;
    public $showDetailsModal = false;
    public $selectedSale = null;
    
    public function mount()
    {
        $this->startDate = $this->startDate ?? now()->format('Y-m-d');
        $this->endDate = $this->endDate ?? now()->format('Y-m-d');
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    #[Computed]
    public function sales()
    {
        $query = Vente::with(['client', 'user', 'details.produit'])
            ->when($this->selectedUserId !== 'all', fn($q) => $q->where('user_id', $this->selectedUserId))
            ->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
            ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
                ->whereYear('created_at', Carbon::parse($this->startDate)->year))
            ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]))
            ->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    #[Computed]
    public function salesSummary()
    {
        return Vente::when($this->selectedUserId !== 'all', fn($q) => $q->where('user_id', $this->selectedUserId))
            ->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
            ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
                ->whereYear('created_at', Carbon::parse($this->startDate)->year))
            ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]))
            ->sum('total');
    }

    #[Computed]
public function salesBySeller()
{
    return User::with(['ventes' => function($q) {
        $q->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
          ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
            ->whereYear('created_at', Carbon::parse($this->startDate)->year))
          ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
              Carbon::parse($this->startDate)->startOfDay(),
              Carbon::parse($this->endDate)->endOfDay()
          ]));
    }])
    ->withSum(['ventes as ventes_sum_total' => function($q) {
        $q->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
          ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
            ->whereYear('created_at', Carbon::parse($this->startDate)->year))
          ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
              Carbon::parse($this->startDate)->startOfDay(),
              Carbon::parse($this->endDate)->endOfDay()
          ]));
    }], 'total')
    ->get()
    ->map(function($user) {
        $filteredSalesCount = $user->ventes
            ->filter(function($vente) {
                return match($this->reportType) {
                    'daily' => $vente->created_at->isSameDay($this->startDate),
                    'monthly' => $vente->created_at->isSameMonth(Carbon::parse($this->startDate)),
                    'custom' => $vente->created_at->between(
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ),
                    default => true
                };
            })->count();

        return [
            'name' => $user->name,
            'total' => $user->ventes_sum_total ?? 0,
            'sales_count' => $filteredSalesCount,
        ];
    });
}


    public function generateReport()
    {
        $this->resetPage();
    }

        public function showDetails($saleId)
    {
        $this->selectedSale = Vente::with(['client', 'user', 'details.produit'])->find($saleId);
        $this->showDetailsModal = true;
    }

    public function resetFilters()
{
    $this->reset([
        'reportType', 
        'selectedUserId',
        'startDate',
        'endDate'
    ]);
    
    // Réinitialiser aux valeurs par défaut
    $this->reportType = 'daily';
    $this->selectedUserId = 'all';
    $this->startDate = now()->format('Y-m-d');
    $this->endDate = now()->format('Y-m-d');
    
    $this->resetPage(); // Réinitialiser aussi la pagination si nécessaire
}
    public function hideDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedSale = null;
    }

    public function printInvoice($saleId)
{
    $this->dispatch('openNewTab', 
        url: route('ventes.direct-print', ['vente' => $saleId])
    );
    
    return redirect()->back();
}

    public function exportReport()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => $this->reportType === 'custom' ? 'required|date|after_or_equal:startDate' : 'nullable',
        ]);

        if ($this->exportType === 'pdf') {
            return $this->exportToPdf();
        } else {
            return $this->exportToExcel();
        }
    }

    public function exportToPdf()
{
    $sales = Vente::with(['client', 'user', 'details.produit'])
        ->when($this->selectedUserId !== 'all', fn($q) => $q->where('user_id', $this->selectedUserId))
        ->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
        ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
            ->whereYear('created_at', Carbon::parse($this->startDate)->year))
        ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ]))
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculer la largeur nécessaire en fonction du contenu
    $baseWidth = 100; // Largeur de base en mm
    $perColumnWidth = 20; // Largeur supplémentaire par colonne si nécessaire
    $calculatedWidth = $baseWidth + (count($sales) * $perColumnWidth); // Exemple de calcul
    
    // Dimensions en points (1 mm = 2.83 points)
    $heightInPoints = 800 * 2.83; // 80 cm en points
    $widthInPoints = $calculatedWidth * 2.83; // Largeur calculée en points

    $pdf = Pdf::loadView('exports.sales2-pdf', [
        'sales' => $sales,
        'startDate' => $this->startDate,
        'endDate' => $this->reportType === 'custom' ? $this->endDate : $this->startDate,
        'reportType' => $this->reportType,
        'selectedUser' => $this->selectedUserId !== 'all' ? User::find($this->selectedUserId) : null,
        'salesSummary' => $this->salesSummary,
        'salesBySeller' => $this->salesBySeller,
        'includeCharts' => $this->includeCharts,
        'includeDetails' => $this->includeDetails,        'orientation' => $this->pdfOrientation, // Assurez-vous que cette ligne est présente

    ]);

    // Définir le format personnalisé
    $pdf->setPaper([0, 0, $widthInPoints, $heightInPoints], 'portrait')
        ->setOption('margin-top', 0)
        ->setOption('margin-bottom', 0)
        ->setOption('margin-left', 0)
        ->setOption('margin-right', 0);

    $filename = match($this->reportType) {
        'daily' => 'rapport-journalier-' . Carbon::parse($this->startDate)->format('Y-m-d'),
        'monthly' => 'rapport-mensuel-' . Carbon::parse($this->startDate)->format('Y-m'),
        'custom' => 'rapport-personnalise-' . Carbon::parse($this->startDate)->format('Y-m-d') . '-a-' . Carbon::parse($this->endDate)->format('Y-m-d'),
    };

    if ($this->selectedUserId !== 'all') {
        $user = User::find($this->selectedUserId);
        $filename .= '-vendeur-' . Str::slug($user->name);
    }

    $filename .= '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->output()),
        $filename
    );
}
    public function exportToExcel()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => $this->reportType === 'custom' ? 'required|date|after_or_equal:startDate' : 'nullable',
        ]);

        $export = new SalesExport(
            startDate: $this->startDate,
            endDate: $this->reportType === 'custom' ? $this->endDate : $this->startDate,
            reportType: $this->reportType,
            userId: $this->selectedUserId !== 'all' ? $this->selectedUserId : null
        );

        $filename = match($this->reportType) {
            'daily' => 'rapport-journalier-' . Carbon::parse($this->startDate)->format('Y-m-d'),
            'monthly' => 'rapport-mensuel-' . Carbon::parse($this->startDate)->format('Y-m'),
            'custom' => 'rapport-personnalise-' . Carbon::parse($this->startDate)->format('Y-m-d') . '-a-' . Carbon::parse($this->endDate)->format('Y-m-d'),
        };

        if ($this->selectedUserId !== 'all') {
            $user = User::find($this->selectedUserId);
            $filename .= '-vendeur-' . Str::slug($user->name);
        }

        $filename .= '.xlsx';

        return $export->download($filename);
    }

    public function render()
    {
        return view('livewire.sales-report');
    }
}