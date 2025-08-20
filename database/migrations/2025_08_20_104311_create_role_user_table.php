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
        Schema::create('role_user', function (Blueprint $table) {
            // Chave estrangeira para a tabela de utilizadores
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Chave estrangeira para a tabela de roles
            $table->foreignId('role_id')
                ->constrained('roles')
                ->onDelete('cascade');

            // Definir a chave primária composta
            // Isto impede que o mesmo utilizador seja associado à mesma role mais de uma vez
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
