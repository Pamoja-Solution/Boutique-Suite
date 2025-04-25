<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $vente->matricule }}</title>
    <style>
        
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; }
        .header { text-align: center; font-size: 15px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table, .table th, .table td { border: 1px solid black; }
        .table th, .table td { padding: 8px; text-align: left; }
                @page {
            size: 80mm 150mm; /* Largeur 80mm, Hauteur 150mm */
            margin: 5mm; /* Petites marges */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Réduire la taille du texte */
            width: 60mm;
        }

        .header {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .table, .table th, .table td {
            border: 1px solid black;
        }

        .table th, .table td {
            padding: 4px;
            text-align: left;
            font-size: 7px;
        }

        p {
            font-size: 8px;
            margin: 5px 0;
        }

    </style>
</head>
<body>
    <div class="header">Facture #{{ $vente->matricule }}</div>
    <p>Client : {{ $vente->client->nom }}</p>
    <p>Date : {{ $vente->created_at->format('d/m/Y') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Médicament</th>
                <th>Qté</th>
                <th>P. U</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->details as $detail)
            <tr>
                <td>{{ $detail->produit->nom }}</td>
                <td>{{ $detail->quantite }}</td>
                <td>{{ number_format($detail->prix_unitaire, 2) }} Fc</td>
                <td>{{ number_format($detail->quantite * $detail->prix_unitaire, 2) }} Fc</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total : {{ number_format($vente->total, 2) }} Fc</strong></p>
</body>
</html>
