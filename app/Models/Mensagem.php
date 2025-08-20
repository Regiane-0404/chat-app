<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importar o SoftDeletes

class Mensagem extends Model
{
    use HasFactory, SoftDeletes; // Usar o SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conteudo',
        'remetente_id',
        'sala_id',
        'destinatario_id',
    ];
    public function remetente()
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    /**
     * Obtém o utilizador que recebeu a mensagem (em caso de DM).
     */
    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    /**
     * Obtém a sala à qual a mensagem pertence.
     */
    public function sala()
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }
}
//Análise: Uma mensagem pertence a um (belongsTo) remetente, a um destinatário (se for DM), e a uma sala (se for de sala).
