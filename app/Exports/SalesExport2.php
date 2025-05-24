<?php

namespace App\Exports;

use App\Models\Vente;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport2 implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $reportType;
    protected $userId;

    public function __construct($startDate, $endDate, $reportType, $userId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportType = $reportType;
        $this->userId = $userId;
    }

    public function collection()
    {
        // Votre logique pour récupérer les données
        $query = Vente::with(['client', 'user'])
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

        return $query->map(function ($sale) {
            return [
                'Date' => $sale->created_at->format('d/m/Y H:i'),
                'N° Vente' => $sale->matricule,
                'Client' => $sale->client->nom,
                'Vendeur' => $sale->user->name,
                'Montant' => number_format($sale->total, 2) . ' FC',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'N° Vente',
            'Client',
            'Vendeur',
            'Montant'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Date
            'B' => 15, // N° Vente
            'C' => 25, // Client
            'D' => 20, // Vendeur
            'E' => 15, // Montant
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour la première ligne (en-têtes)
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD9D9D9']
                ]
            ],
            'A' => ['numberFormat' => ['formatCode' => 'dd/mm/yyyy hh:mm']],
            // Alignement des montants à droite
            'E' => [
                'numberFormat' => ['formatCode' => '#,##0.00" FC"'],
                'alignment' => ['horizontal' => 'right']
            ],            
            // Bordures pour toutes les cellules
            'A1:E'.$sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}