<?php

namespace App\Exports;

use App\Models\Vente;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        public string $startDate,
        public ?string $endDate,
        public string $reportType,
        public ?int $userId = null
    ) {}

    public function collection()
    {
        return Vente::with(['client', 'user', 'details.produit'])
            ->when($this->userId, fn($q) => $q->where('user_id', $this->userId))
            ->when($this->reportType === 'daily', fn($q) => $q->whereDate('created_at', $this->startDate))
            ->when($this->reportType === 'monthly', fn($q) => $q->whereMonth('created_at', Carbon::parse($this->startDate)->month)
                ->whereYear('created_at', Carbon::parse($this->startDate)->year))
            ->when($this->reportType === 'custom', fn($q) => $q->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'N° Vente',
            'Client',
            'Vendeur',
            'Produits',
            'Quantité Totale',
            'Montant Total',
        ];
    }

    public function map($sale): array
    {
        $products = $sale->details->map(fn($detail) => 
            $detail->produit->nom . ' (' . $detail->quantite . ' x ' . number_format($detail->prix_unitaire, 2) . ' €)'
        )->implode("\n");

        $totalQuantity = $sale->details->sum('quantite');

        return [
            $sale->created_at->format('d/m/Y H:i'),
            $sale->matricule,
            $sale->client->nom,
            $sale->user->name,
            $products,
            $totalQuantity,
            number_format($sale->total, 2) . ' €',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:G' => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function title(): string
    {
        return 'Rapport des ventes';
    }
}