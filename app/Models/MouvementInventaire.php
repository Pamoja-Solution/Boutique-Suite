<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementInventaire extends Model
{
    use HasFactory;

    //protected $table = 'mouvements_inventaire';
    
    protected $fillable = [
        'inventaire_id',
        'produit_id',
        'quantite_theorique',
        'quantite_reelle',
        'ecart',
        'commentaire',
    ];

    public function inventaire()
    {
        return $this->belongsTo(Inventaire::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}