<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Sala;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('sala.{salaId}', function ($user, $salaId) {
    $sala = Sala::find($salaId);
    // Verifica se o utilizador é um membro da sala
    return $sala && $sala->utilizadores->contains($user);
});
