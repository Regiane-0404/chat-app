<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salas', function (Blueprint $table) {
            $table->id(); // Chave Primária
            $table->string('nome', 100);
            $table->string('descricao')->nullable(); // Descrição opcional da sala
            $table->string('avatar_url')->nullable(); // Avatar opcional para a sala
            $table->enum('tipo', ['privada', 'direta'])->default('privada'); // Tipo de sala. 'publica' pode ser adicionado no futuro.

            // Chave Estrangeira para saber quem criou a sala
            $table->foreignId('criado_por_utilizador_id')
                ->nullable() // Permite que o criador seja nulo
                ->constrained('users') // Refere-se à tabela 'users'
                ->onDelete('set null'); // Se o utilizador for apagado, o campo fica nulo mas a sala permanece

            $table->timestamps(); // Colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};
