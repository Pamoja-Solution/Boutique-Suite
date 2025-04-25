<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SousRayon extends Model
{
    //
    protected $fillable=[
        'id',
        'rayon_id',
        'nom',
        'code_emplacement'
    ];
    // Relation avec le rayon parent
    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }
     // Relation avec les produits
     public function produits(): HasMany
     {
         return $this->hasMany(Produit::class);
     }
}
