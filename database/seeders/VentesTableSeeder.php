<?php

namespace Database\Seeders;

use App\Models\Vente;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VentesTableSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::pluck('id');
        $vendeurs = User::where('role', 'vendeur')->pluck('id');

        for ($i = 0; $i < 5; $i++) {
            Vente::create([
                'client_id' => $clients->random(),
                'user_id' => $vendeurs->random(),
                'total' => 0, // Serra mis à jour par les détails
                'matricule' => 'VNT-' . Str::upper(Str::random(8)),
                'created_at' => fake()->dateTimeBetween('-1 year', 'now'),//now()->subDays(rand(0, 90))
            ]);
        }
    }
}