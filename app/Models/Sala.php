<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Obtém o utilizador que criou a sala.
     */
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
            ->withPivot('role_na_sala');
    }

    /**
     * As mensagens que pertencem a esta sala.
     */
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class, 'sala_id');
    }
}
