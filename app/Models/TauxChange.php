<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TauxChange extends Model
{
    //
    protected $table=['taux_changes'];

    protected $fillable = [
        'monnaie_id',
        'taux',
        'date_effet'
    ];

    protected $casts = [
        'taux' => 'decimal:6',
        'date_effet' => 'date'
    ];

    public function monnaie(): BelongsTo
    {
        return $this->belongsTo(Monnaie::class);
    }
}
