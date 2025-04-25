<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicament;
use App\Models\Fournisseur;
use App\Models\Categorie;
use Faker\Factory as Faker;

class MedicamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assurer que nous avons des fournisseurs et des catégories
        $fournisseurs = Fournisseur::all();
        $categories = Categorie::all();

        if ($fournisseurs->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Veuillez d\'abord créer des fournisseurs et des catégories');
            return;
        }

        // Générer 100 médicaments aléatoires
        for ($i = 0; $i < 100; $i++) {
            Medicament::create([
                'nom' => $faker->word . ' ' . $faker->randomNumber(3) . 'mg', // Nom aléatoire avec dosage
                'description' => $faker->sentence(10),
                'prix_vente' => $faker->randomFloat(50, 500, 50000),
                'prix_achat' => $faker->randomFloat(50, 50, 30000),
                'stock' => $faker->numberBetween(10, 500),
                'date_expiration' => $faker->dateTimeBetween('+1 year', '+3 years'),
                'fournisseur_id' => $fournisseurs->random()->id,
                'categorie_id' => $categories->random()->id,
                'matricule' => 'MED-' . strtoupper($faker->unique()->lexify('?????') . '-' . $faker->randomNumber(5, true)),            ]);
        }

        $this->command->info('100 médicaments ont été créés avec succès.');
    }
}
