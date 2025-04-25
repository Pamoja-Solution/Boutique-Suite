<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $table = 'fournisseurs';
    
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
    ];
    public function fournisseur()
    {
        return $this->hasMany(Medicament::class);
    }
}
