<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produit extends Model
{
    protected $fillable =[
        'nom',
        'code_barre',
        'reference_interne',
        'prix_vente',
        'prix_achat',
        'stock',
        'seuil_alerte',
        'sous_rayon_id',
        'fournisseur_id',
        'unite_mesure',
        'taxable',
        'rupture_stock','date_expiration'
    ];
    protected $casts = [
        'date_expiration' => 'date', // ou 'datetime' selon votre besoin
    ];
        // Relation avec le fournisseur
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    // Relation avec le sous-rayon (corrigée)
    public function sousRayon(): BelongsTo
    {
        return $this->belongsTo(SousRayon::class, 'sous_rayon_id');
    }


    public static function generateReference()
    {
        // Exemple simple : Nom du produit + taille ou ID aléatoire
        $prefix = 'PROD'; // ou 'COCA', etc., selon ton besoin
        $random = strtoupper(\Str::random(4)); // ex: "ABCD"
        return $prefix . $random;
    }
    // Relation avec le rayon (via le sous-rayon)
    public function rayon()
    {
        return $this->hasOneThrough(
            Rayon::class,
            SousRayon::class,
            'id', // Clé étrangère sur sous_rayons
            'id', // Clé étrangère sur rayons
            'sous_rayon_id', // Clé locale sur produits
            'rayon_id' // Clé locale sur sous_rayons
        );
    }
}
