<?php

use App\Models\User;
use App\Models\Perfil;

// Teste para verificar se a relação um-para-um entre User e Perfil funciona.
test('a user has one profile', function () {
    // Fase 1: Arrange (Organizar/Preparar)
    // Usamos as nossas factories para criar um Utilizador e, ao mesmo tempo,
    // criar um Perfil associado a ele.
    $user = User::factory()
        ->has(Perfil::factory())
        ->create();

    // Fase 2: Act (Agir)
    // Não há uma ação específica aqui, a ação foi a criação.
    // Agora vamos verificar o resultado.

    // Fase 3: Assert (Verificar/Afirmar)
    // Estamos a afirmar duas coisas para garantir que o teste seja robusto:

    // 1. Verificamos se o perfil do utilizador não é nulo.
    expect($user->perfil)->not->toBeNull();

    // 2. Verificamos se o perfil criado é de facto uma instância da classe Perfil.
    // Isto garante que o relacionamento está a retornar o tipo de objeto correto.
    expect($user->perfil)->toBeInstanceOf(Perfil::class);
});
