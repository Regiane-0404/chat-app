<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Importante
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Mensagem; // Importe o nosso modelo

class MensagemEnviada implements ShouldBroadcast // Implemente a interface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * A instância da mensagem.
     *
     * @var \App\Models\Mensagem
     */
    public $mensagem; // Propriedade pública para os dados

    /**
     * Create a new event instance.
     */
    public function __construct(Mensagem $mensagem)
    {
        $this->mensagem = $mensagem;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Vamos transmitir num canal privado chamado 'sala.{id_da_sala}'
        // Apenas utilizadores autorizados a estar nesta sala poderão ouvir.
        return [
            new PrivateChannel('sala.' . $this->mensagem->sala_id),
        ];
    }
}
