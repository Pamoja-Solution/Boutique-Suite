<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    //protected $fillable = ['nom',  'telephone', 'email','adresse'];
    protected $fillable = [
        'nom',
        'telephone',
        'date_naissance',
        'email',
        'adresse',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];
}
