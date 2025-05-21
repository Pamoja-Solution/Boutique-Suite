<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des ventes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .period { font-size: 16px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des ventes</h1>
        <p class="period">
            Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} 
            au {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </p>
    </div>

    

    @if($includeDetails)
        <h3>Détail des ventes</h3>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td>#{{ $sale->id }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->client->nom ?? 'N/A' }}</td>
                    <td>{{ number_format($sale->total, 0, ',', ' ') }} Fc</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>