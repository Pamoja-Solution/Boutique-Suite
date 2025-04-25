<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rayon;
use App\Models\SousRayon;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Support\Str;

class ProduitSeeder extends Seeder
{
    public function run()
    {
        // Création des fournisseurs
        $fournisseurs = [
            ['nom' => 'Fournisseur Général', 'telephone' => '0102030405'],
            ['nom' => 'Distribagro',  'telephone' => '0203040506'],
            ['nom' => 'Grossiste Alimentaire', 'telephone' => '0304050607'],
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::create($fournisseur);
        }

        // Création des rayons
        $rayons = [
            ['nom' => 'Boissons', 'code' => 'BRV', 'icon' => 'wine-bottle', 'ordre_affichage' => 1],
            ['nom' => 'Produits Laitiers', 'code' => 'LAI', 'icon' => 'cheese', 'ordre_affichage' => 2],
            ['nom' => 'Fruits et Légumes', 'code' => 'FLE', 'icon' => 'apple-alt', 'ordre_affichage' => 3],
            ['nom' => 'Épicerie', 'code' => 'EPI', 'icon' => 'shopping-basket', 'ordre_affichage' => 4],
            ['nom' => 'Surgelés', 'code' => 'SUR', 'icon' => 'snowflake', 'ordre_affichage' => 5],
        ];

        foreach ($rayons as $rayon) {
            Rayon::create($rayon);
        }

        // Création des sous-rayons
        $sousRayons = [
            // Boissons
            ['rayon_id' => 1, 'nom' => 'Eaux', 'code_emplacement' => 'BRV-EAU'],
            ['rayon_id' => 1, 'nom' => 'Sodas', 'code_emplacement' => 'BRV-SOD'],
            ['rayon_id' => 1, 'nom' => 'Jus de fruits', 'code_emplacement' => 'BRV-JUS'],
            
            // Produits Laitiers
            ['rayon_id' => 2, 'nom' => 'Laits', 'code_emplacement' => 'LAI-LAI'],
            ['rayon_id' => 2, 'nom' => 'Yaourts', 'code_emplacement' => 'LAI-YAO'],
            ['rayon_id' => 2, 'nom' => 'Fromages', 'code_emplacement' => 'LAI-FRO'],
            
            // Fruits et Légumes
            ['rayon_id' => 3, 'nom' => 'Fruits frais', 'code_emplacement' => 'FLE-FRU'],
            ['rayon_id' => 3, 'nom' => 'Légumes frais', 'code_emplacement' => 'FLE-LEG'],
            
            // Épicerie
            ['rayon_id' => 4, 'nom' => 'Pâtes et Riz', 'code_emplacement' => 'EPI-PAR'],
            ['rayon_id' => 4, 'nom' => 'Conserves', 'code_emplacement' => 'EPI-CON'],
            
            // Surgelés
            ['rayon_id' => 5, 'nom' => 'Légumes surgelés', 'code_emplacement' => 'SUR-LEG'],
            ['rayon_id' => 5, 'nom' => 'Plats préparés', 'code_emplacement' => 'SUR-PLA'],
        ];

        foreach ($sousRayons as $sousRayon) {
            SousRayon::create($sousRayon);
        }

        // Création des produits
        $produits = [
            // Boissons - Eaux
            [
                'nom' => 'Eau minérale 1L',
                'prix_vente' => 0.80,
                'prix_achat' => 0.50,
                'stock' => 120,
                'seuil_alerte' => 20,
                'fournisseur_id' => 1,
                'sous_rayon_id' => 1,
                'reference_interne' => 'EAU1L',
                'code_barre' => '1234567890123',
                'unite_mesure' => 'bouteille',
                'taxable' => true,
            ],
            [
                'nom' => 'Eau gazeuse 1.5L',
                'prix_vente' => 1.20,
                'prix_achat' => 0.80,
                'stock' => 80,
                'seuil_alerte' => 15,
                'fournisseur_id' => 1,
                'sous_rayon_id' => 1,
                'reference_interne' => 'EAUG1.5L',
                'code_barre' => '1234567890124',
                'unite_mesure' => 'bouteille',
                'taxable' => true,
            ],
            
            // Boissons - Sodas
            [
                'nom' => 'Soda Cola 33cl',
                'prix_vente' => 1.10,
                'prix_achat' => 0.70,
                'stock' => 200,
                'seuil_alerte' => 30,
                'fournisseur_id' => 2,
                'sous_rayon_id' => 2,
                'reference_interne' => 'SODCOLA33',
                'code_barre' => '1234567890125',
                'unite_mesure' => 'canette',
                'taxable' => true,
            ],
            
            // Produits Laitiers - Yaourts
            [
                'nom' => 'Yaourt nature x4',
                'prix_vente' => 2.50,
                'prix_achat' => 1.80,
                'stock' => 60,
                'seuil_alerte' => 10,
                'fournisseur_id' => 3,
                'sous_rayon_id' => 5,
                'reference_interne' => 'YAONAT4',
                'code_barre' => '1234567890126',
                'unite_mesure' => 'pack',
                'taxable' => true,
            ],
            
            // Fruits et Légumes - Fruits frais
            [
                'nom' => 'Pommes Golden',
                'prix_vente' => 2.80,
                'prix_achat' => 1.90,
                'stock' => 45,
                'seuil_alerte' => 5,
                'fournisseur_id' => 2,
                'sous_rayon_id' => 7,
                'reference_interne' => 'POMGOLD1KG',
                'code_barre' => '1234567890127',
                'unite_mesure' => 'kg',
                'taxable' => false,
            ],
        ];

        foreach ($produits as $produit) {
            Produit::create($produit);
        }
    }
}