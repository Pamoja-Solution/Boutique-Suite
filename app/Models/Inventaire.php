<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    use HasFactory;

    protected $table = 'inventaires';
    
    protected $fillable = [
        'reference',
        'motif',
        'notes',
        'statut',
        'user_id',
    ];

    public function mouvements()
    {
        return $this->hasMany(MouvementInventaire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function historiques()
    {
        return $this->morphMany(HistoriqueStock::class, 'source');
    }
}