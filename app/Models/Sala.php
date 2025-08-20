<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sala extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'descricao',
        'avatar_url',
        'tipo',
        'criado_por_utilizador_id',
    ];
    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por_utilizador_id');
    }

    /**
     * Os utilizadores que são membros desta sala.
     */
    public function utilizadores()
    {
        return $this->belongsToMany(User::class, 'sala_utilizadores')
            ->withPivot('role_na_sala') // Importante para podermos aceder à permissão na sala
            ->withTimestamps(); // Se a tabela pivot tiver timestamps
    }

    /**
     * As mensagens que pertencem a esta sala.
     */
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class, 'sala_id');
    }
}


//Análise:
//criador(): Uma sala pertence a um (belongsTo) utilizador criador.
//utilizadores(): Uma sala pertence a muitos (belongsToMany) utilizadores. A linha withPivot('role_na_sala') é super importante, pois permite-nos saber a permissão de cada utilizador na sala.
//mensagens(): Uma sala tem muitas (hasMany) mensagens.