<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TauxChange extends Model
{
    //
    protected $table='taux_change';

    protected $fillable = [
        'monnaie_id',
        'taux',
        'date_effet'
    ];

    protected $casts = [
        'date_effet' => 'date',
        'taux' => 'float',
    ];
    

    public function monnaie(): BelongsTo
    {
        return $this->belongsTo(Monnaie::class);
    }
    
    public function monnaieSource()
    {
        return $this->belongsTo(Monnaie::class, 'monnaie_source_id');
    }

    public function monnaieCible()
    {
        return $this->belongsTo(Monnaie::class, 'monnaie_cible_id');
    }
}
