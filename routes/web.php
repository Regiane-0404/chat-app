<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Chat\ChatIndex;
use App\Livewire\Chat\GerirMembros;

// Rota Raiz: Redireciona para o chat
Route::get('/', function () {
    return redirect()->route('chat.index');
});

// Grupo de rotas que exigem autenticação
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Rota de "casa" (para onde o sistema redireciona internamente)
    // Aponta para a nossa rota do chat
    Route::get('/home', function () {
        return redirect()->route('chat.index');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/chat', ChatIndex::class)->name('chat.index');

    Route::get('/salas/{sala}/membros', GerirMembros::class)->name('salas.membros');
});