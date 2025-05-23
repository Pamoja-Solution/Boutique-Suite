<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Codes-barres</title>
    <style>
        @page { margin: 0; padding: 0; size: 80mm auto; }

        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 9px;
            margin: 0;
            padding: 5mm;
        }
        .barcode-item {
            width: 100%;
            padding: 2mm 0;
            text-align: center;
            border-bottom: 1px dashed #ccc;
        }
        .product-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 2px;
            word-break: break-word;
        }
        .barcode-image {
            margin: 2px 0;
        }
        .barcode-image img {
            max-width: 100%;
            height: auto;
        }
        .barcode-number {
            font-family: monospace;
            font-size: 8px;
            margin: 2px 0;
        }
        .product-price {
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }
        .product-ref {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    @foreach($produits as $produit)
        <div class="barcode-item">
            <div class="product-name">{{ $produit['nom'] }}</div>
            <div class="barcode-image">
                <img src="data:image/png;base64,{{ $produit['barcode_image'] }}" alt="Code-barres">
            </div>
            <div class="barcode-number">{{ $produit['code_barre'] }}</div>
            <div class="product-price">{{ number_format($produit['prix_vente'], 2) }}FC</div>
            <div class="product-ref">Réf: {{ $produit['reference_interne'] }}</div>
        </div>
    @endforeach
</body>
</html>