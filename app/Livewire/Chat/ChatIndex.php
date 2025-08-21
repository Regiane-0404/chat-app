<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Sala;

class ChatIndex extends Component
{
    public $salas;
    public $salaSelecionada = null;
    public $mensagens; // Propriedade para as mensagens
    public string $novaMensagem = ''; // Nova propriedade para o input

    public function mount(): void
    {
        $user = auth()->user();
        $this->salas = $user->salas()->get();
    }

    public function selecionarSala(int $salaId): void
    {
        $this->salaSelecionada = Sala::findOrFail($salaId);
        // Ao selecionar a sala, carregamos as suas mensagens usando o relacionamento
        $this->mensagens = $this->salaSelecionada->mensagens()->get();
    }

    /**
     * Guarda a nova mensagem na base de dados.
     */
    public function enviarMensagem(): void
    {
        // 1. Validar que a sala está selecionada e a mensagem não está vazia
        if (!$this->salaSelecionada || empty(trim($this->novaMensagem))) {
            return;
        }

        // 2. Criar a mensagem na base de dados
        $this->salaSelecionada->mensagens()->create([
            'remetente_id' => auth()->id(),
            'conteudo' => $this->novaMensagem,
        ]);

        // 3. Limpar a caixa de texto
        $this->reset('novaMensagem');

        // 4. Recarregar as mensagens para mostrar a nova
        $this->mensagens = $this->salaSelecionada->mensagens()->get();
    }

    public function render(): View
    {
        return view('livewire.chat.chat-index')->layout('layouts.app');
    }
}
