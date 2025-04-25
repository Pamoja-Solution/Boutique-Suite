<?php
// database/seeders/FournisseurSeeder.php
namespace Database\Seeders;

use App\Models\Fournisseur;
use Illuminate\Database\Seeder;

class FournisseurSeeder extends Seeder
{
    public function run()
    {
        Fournisseur::create(['nom' => 'PharmaPlus', 'telephone' => '771234567', 'adresse' => 'Dakar']);
        Fournisseur::create(['nom' => 'MediCare', 'telephone' => '761234567', 'adresse' => 'Thiès']);
        Fournisseur::create(['nom' => 'SantéPro', 'telephone' => '701234567', 'adresse' => 'Saint-Louis']);
    }
}