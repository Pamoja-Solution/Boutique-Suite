<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'nom' => fake()->name(),
            'telephone' => fake()->phoneNumber(),
            'date_naissance' => $this->faker->dateTimeBetween('-67 year', 'now'), // Date de création
            'email' => fake()->unique()->safeEmail(), // Dernière mise à jour
        
        ];
    }
}
