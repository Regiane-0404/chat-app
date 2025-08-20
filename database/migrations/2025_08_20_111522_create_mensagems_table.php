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
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id(); // Chave Primária
            $table->text('conteudo'); // O texto da mensagem

            // Chave estrangeira para o remetente (quem enviou)
            $table->foreignId('remetente_id')
                ->constrained('users')
                ->onDelete('cascade'); // Se o remetente for apagado, as suas mensagens também são

            // Chave estrangeira para a sala (PODE SER NULA, caso seja uma DM)
            $table->foreignId('sala_id')
                ->nullable()
                ->constrained('salas')
                ->onDelete('cascade'); // Se a sala for apagada, as mensagens nela também são

            // Chave estrangeira para o destinatário (PODE SER NULA, caso seja para uma sala)
            // Nota: O nome da coluna é 'user_id' e não 'para_utilizador_id' para seguir a convenção do Laravel,
            // o que facilita os relacionamentos. Vamos ajustar isto no nosso modelo.
            $table->foreignId('destinatario_id')
                ->nullable()
                ->constrained('users') // Refere-se à tabela 'users'
                ->onDelete('cascade'); // Se o destinatário for apagado, as DMs para ele são apagadas

            $table->timestamps(); // Colunas created_at e updated_at
            $table->softDeletes(); // Adiciona a coluna 'deleted_at' para "apagar de forma suave"
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagems');
    }
};
