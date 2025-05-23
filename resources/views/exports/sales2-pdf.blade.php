<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des ventes</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            width: auto; 
            max-width: 100%; 
            margin: 0;
            padding: 0;
        }
        .container {
            width: auto;
            min-width: 100%;
        }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-bottom: 20px; }
        .page-break { page-break-after: always; }
        .seller-performance { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Rapport des ventes</div>
        <div class="subtitle">
            @if($reportType === 'daily')
                Pour le {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            @elseif($reportType === 'monthly')
                Pour {{ Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
            @else
                Du {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @endif
        </div>
        @if($selectedUser)
            <div class="subtitle">Vendeur: {{ $selectedUser->name }}</div>
        @endif
    </div>

    <div class="summary">
        <table class="table">
            <tr>
                <th>Total des ventes</th>
                <td class="text-right">{{ number_format($salesSummary, 2) }} FC</td>
            </tr>
            <tr>
                <th>Nombre de ventes</th>
                <td class="text-right">{{ $sales->count() }}</td>
            </tr>
        </table>
    </div>

    @if($includeCharts && $selectedUser === null && $salesBySeller->isNotEmpty())
        <div class="seller-performance">
            <h3>Performance par vendeur</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Vendeur</th>
                        <th class="text-right">Nombre de ventes</th>
                        <th class="text-right">Total des ventes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesBySeller as $seller)
                        <tr>
                            <td>{{ $seller['name'] }}</td>
                            <td class="text-right">{{ $seller['sales_count'] }}</td>
                            <td class="text-right">{{ number_format($seller['total'], 2) }} FC</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($includeDetails)
        @if($orientation === 'landscape')
            <div class="page-break"></div>
        @endif

        <h3>Détail des ventes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Vente</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->matricule }}</td>
                        <td>{{ $sale->client->nom }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td class="text-right">{{ number_format($sale->total, 2) }} FC</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>