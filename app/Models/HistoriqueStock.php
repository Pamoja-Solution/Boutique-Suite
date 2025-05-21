<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueStock extends Model
{
    use HasFactory;

    protected $table = 'historique_stock';
    
    protected $fillable = [
        'produit_id',
        'quantite',
        'stock_avant',
        'stock_apres',
        'type_mouvement',
        'user_id',
        'source_id',
        'source_type',
        'commentaire',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}