<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rayon extends Model
{
    protected $fillable=[
'nom',
'code',
'description',
'icon',
'ordre_affichage',
'id'
    ];
     // Relation avec les sous-rayons
     public function sousRayons(): HasMany
     {
         return $this->hasMany(SousRayon::class);
     }
    
}
