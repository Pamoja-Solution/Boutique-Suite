<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rayon;
use App\Models\SousRayon;
use App\Models\Fournisseur;
use App\Models\Produit;
use Faker\Factory as Faker;

class RandomProduitSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');

        // Création de 5 fournisseurs aléatoires
        for ($i = 1; $i <= 5; $i++) {
            Fournisseur::create([
                'nom' => $faker->company,
                'telephone' => $faker->phoneNumber,
                'adresse' => $faker->address,
            ]);
        }

        // Rayons principaux avec icônes
        $rayons = [
            ['nom' => 'Boissons', 'code' => 'BRV', 'icon' => 'wine-bottle'],
            ['nom' => 'Produits Laitiers', 'code' => 'LAI', 'icon' => 'cheese'],
            ['nom' => 'Fruits et Légumes', 'code' => 'FLE', 'icon' => 'apple-alt'],
            ['nom' => 'Épicerie Sèche', 'code' => 'EPI', 'icon' => 'shopping-basket'],
            ['nom' => 'Surgelés', 'code' => 'SUR', 'icon' => 'snowflake'],
            ['nom' => 'Boulangerie', 'code' => 'BOU', 'icon' => 'bread-slice'],
            ['nom' => 'Viandes', 'code' => 'VIA', 'icon' => 'drumstick-bite'],
            ['nom' => 'Produits Ménagers', 'code' => 'MEN', 'icon' => 'broom'],
        ];

        /*foreach ($rayons as $index => $rayon) {
            Rayon::create([
                'nom' => $rayon['nom'],
                'code' => $rayon['code'],
                'icon' => $rayon['icon'],
                'description' => $faker->sentence,
                'ordre_affichage' => $index + 1
            ]);
        }*/

        // Sous-rayons aléatoires
        $sousRayonsData = [
            // Boissons
            ['Boissons gazeuses', 'Eaux', 'Jus de fruits', 'Boissons énergisantes', 'Vins'],
            // Produits Laitiers
            ['Laits', 'Yaourts', 'Fromages', 'Crèmes', 'Beurres'],
            // Fruits et Légumes
            ['Fruits frais', 'Légumes frais', 'Fruits exotiques', 'Salades', 'Herbes aromatiques'],
            // Épicerie Sèche
            ['Pâtes et Riz', 'Conserves', 'Biscuits', 'Céréales', 'Huiles'],
            // Surgelés
            ['Légumes surgelés', 'Plats préparés', 'Glaces', 'Poissons surgelés', 'Pizzas'],
            // Boulangerie
            ['Pains', 'Viennoiseries', 'Pâtisseries', 'Sandwichs', 'Gâteaux'],
            // Viandes
            ['Bœuf', 'Poulet', 'Porc', 'Agneau', 'Charcuterie'],
            // Produits Ménagers
            ['Nettoyants', 'Lessives', 'Papiers', 'Désinfectants', 'Parfums d\'ambiance'],
        ];

        /*foreach ($sousRayonsData as $rayonIndex => $sousRayons) {
            foreach ($sousRayons as $sousRayonIndex => $sousRayonNom) {
                SousRayon::create([
                    'rayon_id' => $rayonIndex + 1,
                    'nom' => $sousRayonNom,
                    'code_emplacement' => substr(Rayon::find($rayonIndex + 1)->code, 0, 3) . '-' . 
                                         strtoupper(substr($sousRayonNom, 0, 3)),
                ]);
            }
        }*/

        // Génération de 50 produits aléatoires
        for ($i = 0; $i < 50; $i++) {
            $sousRayon = SousRayon::inRandomOrder()->first();
            $fournisseur = Fournisseur::inRandomOrder()->first();
            
            $nomProduit = $this->generateProductName($faker, $sousRayon->nom);
            $prixAchat = $faker->randomFloat(2, 0.5, 20);
            $marge = $faker->randomFloat(2, 0.2, 0.5); // Marge de 20% à 50%
            $prixVente = $prixAchat * (1 + $marge);
            
            Produit::create([
                'nom' => $nomProduit,
                //'description' => $faker->sentence(10),
                'prix_vente' => round($prixVente, 2),
                'prix_achat' => round($prixAchat, 2),
                'stock' => $faker->numberBetween(0, 200),
                'seuil_alerte' => $faker->numberBetween(5, 20),
                'fournisseur_id' => $fournisseur->id,
                'sous_rayon_id' => $sousRayon->id,
                'reference_interne' => 'PROD-' . strtoupper(substr($nomProduit, 0, 3)) . $faker->unique()->numberBetween(100, 999),
                'code_barre' => $faker->ean13,
                'unite_mesure' => $this->getRandomUnit($sousRayon->nom),
                'taxable' => $faker->boolean(90), // 90% de chance d'être taxable
            ]);
        }
    }

    // Génère des noms de produits réalistes selon le sous-rayon
    private function generateProductName($faker, $sousRayon)
    {
        $prefixes = [
            'Boissons gazeuses' => ['Soda', 'Cola', 'Limonade', 'Tonic', 'Schweppes'],
            'Eaux' => ['Eau minérale', 'Eau de source', 'Eau gazeuse', 'Eau pétillante'],
            'Laits' => ['Lait entier', 'Lait demi-écrémé', 'Lait écrémé', 'Lait bio'],
            'Fromages' => ['Camembert', 'Brie', 'Comté', 'Roquefort', 'Chèvre'],
            'Fruits frais' => ['Pommes', 'Poires', 'Bananes', 'Oranges', 'Raisins'],
            'Légumes frais' => ['Carottes', 'Tomates', 'Salade', 'Concombres', 'Courgettes'],
            'Pâtes et Riz' => ['Spaghetti', 'Penne', 'Fusilli', 'Riz basmati', 'Riz complet'],
            'Conserves' => ['Haricots verts', 'Pois chiches', 'Maïs', 'Tomates pelées'],
        ];

        $prefix = $prefixes[$sousRayon] ?? [$sousRayon];
        $prefix = is_array($prefix) ? $faker->randomElement($prefix) : $prefix;

        return $prefix . ' ' . $faker->word . ' ' . $faker->randomElement(['Bio', 'Premium', 'Classic', 'Nature', 'Original']);
    }

    // Retourne une unité de mesure appropriée
    private function getRandomUnit($sousRayon)
    {
        $units = [
            'Boissons gazeuses' => ['bouteille', 'canette', 'pack'],
            'Eaux' => ['bouteille', 'pack'],
            'Laits' => ['litre', 'brique', 'pack'],
            'Fromages' => ['pièce', 'kg', 'tranche'],
            'Fruits frais' => ['kg', 'barquette', 'pièce'],
            'Légumes frais' => ['kg', 'pièce', 'sachet'],
            'Pâtes et Riz' => ['kg', 'paquet', 'sachet'],
            'default' => ['unité', 'pièce', 'kg', 'litre'],
        ];

        foreach ($units as $key => $unitList) {
            if (str_contains($sousRayon, $key)) {
                return $unitList[array_rand($unitList)];
            }
        }

        return $units['default'][array_rand($units['default'])];
    }
}