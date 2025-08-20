<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sala>
 */
class SalaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->sentence(3), // Gera uma frase curta para o nome da sala
            'descricao' => fake()->sentence(10), // Gera uma frase mais longa para a descrição
            'avatar_url' => null,
            'tipo' => fake()->randomElement(['privada', 'direta']),
            // Nota: O 'criado_por_utilizador_id' é uma chave estrangeira.
            // Não a definimos aqui. Vamos associá-la através de relacionamentos,
            // assim como fizemos com User e Perfil.
        ];
    }
}
