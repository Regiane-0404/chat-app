<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id(); // Chave Primária (id BIGINT UNSIGNED AUTO_INCREMENT)

            // Chave Estrangeira que referencia a tabela 'users'
            $table->foreignId('utilizador_id')
                ->constrained('users') // constrained() diz ao Laravel para usar a convenção padrão (tabela 'users', coluna 'id')
                ->onDelete('cascade'); // Se um utilizador for apagado, o seu perfil também será

            $table->string('nome', 100); // Nome de exibição do utilizador
            $table->string('avatar_url')->nullable(); // URL para a imagem de avatar, pode ser nulo
            $table->enum('estado', ['online', 'offline', 'ausente', 'ocupado'])->default('offline'); // Estado de presença

            $table->timestamps(); // Colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfils');
    }
};
