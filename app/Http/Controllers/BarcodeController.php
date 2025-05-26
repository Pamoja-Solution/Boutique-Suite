<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Ajustez selon votre modèle
use App\Models\Produit;

class BarcodeController extends Controller
{
    // Afficher la page du scanner
    public function index()
    {
        return view('scanner.index');
    }
    
    // Traiter les scans
    public function processScan(Request $request)
    {
        $barcode = $request->input('barcode');
        // Votre logique de traitement
        $product = Produit::where('code_barre', $barcode)->first();
        dd( $product);
        
        return response()->json([
            'success' => !!$product,
            'product' => $product,
            'message' => $product ? 'Produit trouvé' : 'Produit non trouvé'
        ]);
    }
}