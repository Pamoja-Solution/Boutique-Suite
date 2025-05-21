<?php

namespace App\Http\Controllers;

use App\Models\Inventaire;
use App\Models\MouvementInventaire;
use Illuminate\Http\Request;

class InventaireController extends Controller
{
    public function index()
    {
        return view('inventaires.index');
    }
    
    public function create()
    {
        return view('inventaires.create');
    }
    
    public function edit($id)
    {
        return view('inventaires.edit', ['inventaireId' => $id]);
    }
    
    public function show($id)
    {
        $inventaire = Inventaire::findOrFail($id);
        $mouvements = MouvementInventaire::with('produit.sousRayon.rayon')
            ->where('inventaire_id', $id)
            ->get()
            ->map(function ($mouvement) {
                // Calcul de l'écart pour chaque mouvement
                $mouvement->ecart = $mouvement->quantite_reelle !== null 
                    ? $mouvement->quantite_reelle - $mouvement->quantite_theorique 
                    : null;
                return $mouvement;
            });
            
        $totalProduits = $mouvements->count();
        $ecartPositifs = $mouvements->where('ecart', '>', 0)->count();
        $ecartNegatifs = $mouvements->where('ecart', '<', 0)->count();
        $sansEcart = $mouvements->where('ecart', 0)->count();
        $nonRenseignes = $mouvements->where('ecart', null)->count();
        
        return view('inventaires.show', compact(
            'inventaire', 
            'mouvements', 
            'totalProduits', 
            'ecartPositifs', 
            'ecartNegatifs', 
            'sansEcart',
            'nonRenseignes'
        ));
    }
    
    public function mouvements($id)
    {
        return view('inventaires.mouvements', ['inventaireId' => $id]);
    }
}