<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des ventes</title>
    <style>
        @page { 
            margin: 0;
            padding: 0;
            size: 80mm auto;
        }
        
        body {
            font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 2mm;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px dashed #000;
        }
        
        .title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 1mm;
        }
        
        .subtitle {
            font-size: 9px;
            margin-bottom: 1mm;
        }
        
        .table {
            width: 90%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 2mm 0;
        }
        
        .table th {
            text-align: left;
            padding: 1mm 0;
            border-bottom: 1px solid #000;
        }
        
        .table td {
            padding: 1mm 0;
            border-bottom: 1px dashed #ccc;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">RAPPORT DES VENTES</div>
        <div class="subtitle">
            @if($reportType === 'daily')
                DATE: {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            @elseif($reportType === 'monthly')
                MOIS: {{ Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
            @else
                PERIODE: {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @endif
        </div>
        @if($selectedUser)
            <div class="subtitle">VENDEUR: {{ $selectedUser->name }}</div>
        @endif
    </div>

    <table class="table">
        <tr>
            <td><strong>TOTAL VENTES</strong></td>
            <td class="text-right">{{ number_format($salesSummary, 2) }} FC</td>
        </tr>
        <tr>
            <td><strong>NOMBRE VENTES</strong></td>
            <td class="text-right">{{ $sales->count() }}</td>
        </tr>
    </table>

    @if($includeCharts && $selectedUser === null && $salesBySeller->isNotEmpty())
        <div class="divider"></div>
        <div class="text-center"><strong>PERF. PAR VENDEUR</strong></div>
        <table class="table">
            @foreach($salesBySeller as $seller)
                <tr>
                    <td>{{ Str::limit($seller['name'], 15) }}</td>
                    <td class="text-right">{{ $seller['sales_count'] }}</td>
                    <td class="text-right">{{ number_format($seller['total'], 2) }} FC</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div style="">
        <h2>Détails des produits vendus</h2>
        <table style="table">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th class="text-right">Produit</th>
                    <th class="text-right">Q .V</th>
                    <th class="text-right">St. rest</th>
                    <th class="text-right">Total vendu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productsSold as $product)
                    <tr>
                        <td >{{ $product['nom'] }}</td>
                        <td>{{ $product['quantite_vendue'] }}</td>
                        <td>{{ $product['quantite_restante'] }}</td>
                        <td>{{ number_format($product['total_vendu'], 2) }} FC</td>
                    </tr>
                @endforeach
            </tbody>
            @if(count($productsSold) > 0)
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>{{ array_sum(array_column($productsSold->toArray(), 'quantite_vendue')) }}</th>
                        <th></th>
                        <th>{{ number_format(array_sum(array_column($productsSold->toArray(), 'total_vendu')), 2) }} FC</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>


    @if($includeDetails)
        <div class="divider"></div>
        <div class="text-center"><strong>DETAIL DES VENTES</strong></div>
        <table class="table">
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m H:i') }}</td>
                    <td>{{ $sale->matricule }}</td>
                    <td class="text-right">{{ number_format($sale->total, 2) }} FC</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="divider"></div>
    <div class="text-center">*** MERCI ***</div>

</body>
</html>