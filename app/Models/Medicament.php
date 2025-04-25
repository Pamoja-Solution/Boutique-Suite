<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicament extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'prix_vente', 'prix_achat', 'stock','fournisseur_id','categorie_id','matricule', 'date_expiration'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicament) {
            $medicament->matricule = self::generateMatricule();
        });
    }
    public function detailsVente()
    {
        return $this->hasMany(DetailVente::class);
    }
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class); 
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class); 
    }
    

    protected $casts = [
        'date_expiration' => 'datetime',
    ];

   

public static function generateMatricule()
{
    $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5)); // 5 lettres aléatoires
    $numbers = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT); // 5 chiffres aléatoires

    return 'MED-' . $letters . '-' . $numbers;
}

}
