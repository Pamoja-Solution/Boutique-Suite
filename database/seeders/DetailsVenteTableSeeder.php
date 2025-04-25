<?php

namespace Database\Seeders;

use App\Models\DetailVente;
use App\Models\Vente;
use App\Models\Medicament;
use Illuminate\Database\Seeder;

class DetailsVenteTableSeeder extends Seeder
{
    public function run()
    {
        // Vérifiez d'abord qu'il y a des médicaments et des ventes
        if (Medicament::count() === 0 || Vente::count() === 0) {
            $this->command->warn('Aucun médicament ou vente trouvé. Veuillez d\'abord exécuter les seeders pour les médicaments et les ventes.');
            return;
        }

        $ventes = Vente::all();
        $medicaments = Medicament::all();

        foreach ($ventes as $vente) {
            $totalVente = 0;
            $nbMedicaments = rand(1, 5);

            // Prendre des médicaments aléatoires sans répétition pour une même vente
            $medicamentsSelectionnes = $medicaments->random(min($nbMedicaments, $medicaments->count()));

            foreach ($medicamentsSelectionnes as $medicament) {
                $qte = rand(1, 10);

                DetailVente::create([
                    'vente_id' => $vente->id,
                    'medicament_id' => $medicament->id,
                    'quantite' => $qte,
                    'prix_unitaire' => $medicament->prix_vente
                ]);

                $totalVente += $medicament->prix_vente * $qte;
            }

            // Mettre à jour le total de la vente
            $vente->update(['total' => $totalVente]);
        }
    }
}