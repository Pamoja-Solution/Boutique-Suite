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
        return User::withSum(['ventes' => function($q) {
            $q->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
              ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
                ->whereYear('created_at', Carbon::parse($this->startDate)->year))
              ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
                  Carbon::parse($this->startDate)->startOfDay(),
                  Carbon::parse($this->endDate)->endOfDay()
              ]));
        }], 'total')
        ->where('role', 'vendeur')
        ->orderBy('ventes_sum_total', 'desc')
        ->get()
        ->map(fn($user) => [
            'name' => $user->name,
            'total' => $user->ventes_sum_total ?? 0,
            'sales_count' => $user->ventes()->count()
        ]);
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

        $pdf = Pdf::loadView('exports.sales2-pdf', [
            'sales' => $sales,
            'startDate' => $this->startDate,
            'endDate' => $this->reportType === 'custom' ? $this->endDate : $this->startDate,
            'reportType' => $this->reportType,
            'selectedUser' => $this->selectedUserId !== 'all' ? User::find($this->selectedUserId) : null,
            'salesSummary' => $this->salesSummary,
            'salesBySeller' => $this->salesBySeller,
            'includeCharts' => $this->includeCharts,
            'includeDetails' => $this->includeDetails,
            'orientation' => $this->pdfOrientation,
        ]);

        $pdf->setPaper('A4', $this->pdfOrientation);

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