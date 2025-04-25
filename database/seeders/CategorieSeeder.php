<?php

// database/seeders/CategorieSeeder.php
namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    public function run()
    {
        Categorie::create(['nom' => 'Antidouleurs']);
        Categorie::create(['nom' => 'Anti-inflammatoires']);
        Categorie::create(['nom' => 'Antibiotiques']);
        Categorie::create(['nom' => 'Vitamines']);
    }
}
