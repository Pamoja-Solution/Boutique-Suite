<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ticket #{{ $vente->matricule }}</title>
    <style>
        @page { margin: 0; padding: 0; size: 80mm auto; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 9px;
            margin: 0;
            padding: 5mm;
        }
        .header { 
            text-align: center; 
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #ccc;
        }
        .header h1 {
            font-size: 12px;
            margin: 2px 0;
        }
        .header p {
            margin: 2px 0;
        }
        .header-logo {
            max-height: 30px;
            max-width: 100%;
            margin-bottom: 5px;
        }
        .info-block {
            margin: 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px dashed #ccc;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .items-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            padding: 2px 0;
            border-bottom: 1px solid #ddd;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .items-table .qty {
            white-space: nowrap;
        }
        .total-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 3px 0;
        }
        .total-table tr:last-child td {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
        }
        .barcode {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>{{ $entreprise['nom'] }}</h1>
        <img src="{{ $entreprise['logo'] }}" class="header-logo">
        <p>{{ $entreprise['adresse'] }}</p>
        <p>Tél: {{ $entreprise['telephone'] }} • {{ $entreprise['email'] }}</p>
        <p>Site web: 
            <a href="{{ $entreprise['site_web'] }}" target="_blank" rel="noopener noreferrer">{{ $entreprise['site_web'] }}</a>
        </p>
    </div>

    <div class="info-block">
        <div class="info-row">
            <span class="bold">Ticket #</span>
            <span>{{ $vente->matricule }}</span>
        </div>
        <div class="info-row">
            <span class="bold">Date:</span>
            <span>{{ $vente->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($vente->client)
        <div class="info-row">
            <span class="bold">Client:</span>
            <span>{{ $vente->client->nom }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="bold">Vendeur:</span>
            <span>{{ $vente->user->name }}</span>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Article</th>
                <th class="text-right">Prix Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->details as $detail)

            <tr>
                <td>
                    {{ $detail->produit->nom }}<br>
                    <span class="qty">Qt :{{ $detail->quantite }} × {{ number_format($detail->prix_unitaire, 2) }} FC </span>
                </td>
                <td class="text-right">{{ number_format($detail->quantite * $detail->prix_unitaire, 2) }} FC</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <!--tr>
            <td>Total HT:</td>
            <td class="text-right">{{ number_format($vente->total_ht, 2) }} CDF</td>
        </tr>
        <tr>
            <td>TVA:</td>
            <td class="text-right">{{ number_format($vente->total_tva, 2) }} CDF</td>
        </tr-->
        <tr>
            <td class="bold">Total Général:</td>
            <td class="text-right bold">{{ number_format($vente->total, 2) }} CDF</td>
        </tr>
       
    </table>

    <div class="footer">
        <p>Merci pour votre achat !</p>
        <p>{{ date('d/m/Y H:i') }}</p>
        <div class="barcode">
            *{{ $vente->matricule }}*
        </div>
    </div>
</body>
</html>