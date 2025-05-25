<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ticket #{{ $vente->matricule }}</title>
    <style>
        @page { margin: 0; padding: 0; size: 72mm auto; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 9px;
            margin: 0;
            padding: 2mm;
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
            margin: 1px 0;
            
        }
        .total-table td {
            padding: 1px 0;
        }
        .total-table tr:last-child td {
            
            padding-top: 1px;
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

        .table, th, .td {
            border: 1px solid;
            border-collapse: collapse;
            
        }
        td, tr, th{
            height: 20px!important;
            
            
        }
        th, td {
            padding-left: 4px;
            padding-right: 4px;
        }
        .align {
            text-align: center;
            align-items: center;
            align-content: center;
        }


    .table-container {
        width: 100%;
        margin: 0 auto;
    }
    
    .table {
        width: 100%;
        max-width: 88mm; /* Largeur maximale de 88mm */
        border-collapse: collapse;
        table-layout: fixed; /* Essentiel pour le contrôle précis */
        margin: 0 auto;
    }
    
    .table th, .table td {
        border: 2px dotted;
        padding: 2px;
        height: auto;
        word-wrap: break-word;
    }
    
    /* Répartition des colonnes */
    .table th:nth-child(1), 
    .table td:nth-child(1) {
        width: 12%; /* Qté */
    }
    
    .table th:nth-child(2), 
    .table td:nth-child(2) {
        width: 40%; /* Article */
    }
    
    .table th:nth-child(3), 
    .table td:nth-child(3) {
        width: 16%; /* P.U. */
    }
    
    .table th:nth-child(4), 
    .table td:nth-child(4) {
        width: 23%; /* P.T. */
    }
    
    .align {
        text-align: center;
    }
    
    .text-right {
        text-align: right;
    }

    </style>
</head>
<body>
    
    <div class="header">
        <h1>{{ $entreprise['nom'] }}</h1>
        <p>{{ $entreprise['adresse'] }}</p>
        <p>Tél: {{ $entreprise['telephone'] }} </p>
        <p style="font-weight: bold">
            {{ $entreprise['rccm'] }}
        </p>
        <p style="font-weight: bold">FACTURE DE VENTE</p>
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
    <div class="table-container">
    <table class="table">
        <thead>
            <tr class="align">
                <th class="align">Qté</th>
                <th>Article</th>
                <th class="align">P . U</th>
                <th class="align">P . T</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->details as $detail)

            <tr class="td">
                <td class="align td" style="font-weight: bold">
                    <span   >{{ $detail->quantite }} </span>
                </td>
                <td class="td">
                    {{ \Str::limit($detail->produit->nom ,20)}}<br>
                </td>
                <td class="td">
                    {{ number_format($detail->prix_unitaire, 0, '.', ' ') }} 
                </td>
                <td class="text-right td">{{ number_format($detail->quantite * $detail->prix_unitaire, 1, '.', ' ') }} </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top: 5px">
    <span class="">QTÉ TOTAL  :</span>
    <span class="text-right bold">{{ ($vente->details)->sum('quantite'); }}</span>
</div>
    <table class="total-table" >
        
        
        <tr>
            <td class="bold">TOTAL GÉNÉRAL :</td>
            <td class="text-right bold">{{ number_format($vente->total, 2) }} FC</td>
            
        </tr>
        @if ($monnaie)
                                    <div class="mt-2 text-sm font-bold text-base-content/70">
                                        DOLLAR: <span class="bold">{{ number_format($vente->total / $monnaie->taux_change, 2) }}{{ $monnaie->symbole}}</span>
                                    </div>
                                    
                                @endif
       
    </table>
    {{ ucfirst(\App\Helpers\NumberToWordsHelper::toWordsWithDecimals($vente->total)) }} Franc Congolais


    <div class="footer">
        <p>Merci pour votre achat !</p>
        <p>{{ date('d/m/Y H:i') }}</p>
        <div class="barcode-container" style="text-align: center; margin: 15px 0;">
            <!-- Code-barre image -->
            <!--img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($vente->matricule, 'C128') }}" 
                 alt="Code-barre {{ $vente->matricule }}"
                 style="height: 25px; width: auto; max-width: 100%; image-rendering: crisp-edges;"-->
            
            <!-- Numéro de matricule -->
            <div class="barcode-number" style="
                font-family: 'Courier New', monospace;
                font-size: 14px;
                letter-spacing: 2px;
                margin-top: 5px;
                font-weight: bold;
                background: #f8f8f8;
                display: inline-block;
                padding: 3px 10px;
                border-radius: 4px;
            ">
                *{{ $vente->matricule }}*
            </div>
        </div>
    </div>
</body>
</html>


<!--DOCTYPE html>
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
            <p>{{ $entreprise['adresse'] }}</p>
            <p>Tél: {{ $entreprise['telephone'] }} </p>
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
</html-->