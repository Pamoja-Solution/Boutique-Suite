<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monnaie extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "monnaies";
    protected $fillable = [
        'libelle', 
        'symbole', 
        'code',
        'taux_change',
        'statut'
    ];

    protected $casts = [
        'taux_change' => 'decimal:6',
        'statut' => 'boolean'
    ];

    

    public function tauxChanges()
    {
        return $this->hasMany(TauxChange::class);
    }
    
}
