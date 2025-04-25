<?php

namespace App\Exports;

use App\Models\Vente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Vente::with('client')->get()->map(function ($vente) {
            return [
                'ID' => $vente->id,
                'Client' => $vente->client->nom ?? 'N/A',
                'Total' => $vente->total,
                'Date' => $vente->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Client', 'Total', 'Date'];
    }
}

