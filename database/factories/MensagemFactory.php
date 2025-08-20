<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mensagem>
 */
class MensagemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conteudo' => fake()->paragraph(), // Gera um parágrafo de texto falso
            // Todas as outras colunas (remetente_id, sala_id, etc.)
            // são chaves estrangeiras e serão preenchidas através
            // dos relacionamentos nos nossos testes.
        ];
    }
}
