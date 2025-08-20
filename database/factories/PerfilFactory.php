<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perfil>
 */
class PerfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'avatar_url' => null, // Por defeito, um perfil não tem avatar
            'estado' => fake()->randomElement(['online', 'offline', 'ausente', 'ocupado']),
        ];
    }
}
