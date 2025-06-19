<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(122)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Client::factory(30)->create();

*/

Client::factory(130)->create();
        $this->call([

           //FournisseurSeeder::class,
           //CategorieSeeder::class,
            //MedicamentSeeder::class,
            //VentesTableSeeder::class,
            //DetailsVenteTableSeeder::class,
        ]);
    }
}
