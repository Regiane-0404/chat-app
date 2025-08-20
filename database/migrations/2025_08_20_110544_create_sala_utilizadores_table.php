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
        Schema::create('sala_utilizadores', function (Blueprint $table) {
            // Chave estrangeira para a tabela 'salas'
            $table->foreignId('sala_id')
                ->constrained('salas')
                ->onDelete('cascade'); // Se a sala for apagada, as "inscrições" nela são removidas

            // Chave estrangeira para a tabela 'users'
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // Se o utilizador for apagado, as suas "inscrições" são removidas

            // Coluna para a permissão do utilizador DENTRO da sala
            $table->enum('role_na_sala', ['membro', 'moderador', 'owner'])->default('membro');

            // Chave primária composta para evitar entradas duplicadas
            $table->primary(['sala_id', 'user_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sala_utilizadores');
    }
};
