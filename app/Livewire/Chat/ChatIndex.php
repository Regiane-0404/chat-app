<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Sala;
use App\Models\Mensagem;
use App\Events\MensagemEnviada;

class ChatIndex extends Component
{
    public bool $mostrandoModalNovaSala = false;
    public string $nomeNovaSala = '';
    public $salas;
    public ?Sala $salaSelecionada = null;
    public $mensagens = [];
    public string $novaMensagem = '';

    /**
     * Define os "ouvintes" de eventos dinamicamente.
     * Esta é a peça chave para o tempo real.
     */
    public function getListeners()
    {
        // Se nenhuma sala estiver selecionada, não ouve nada.
        if (!$this->salaSelecionada) {
            return [];
        }

        // Se uma sala estiver selecionada, ouve o canal privado específico dela.
        return [
            "echo-private:sala.{$this->salaSelecionada->id},.App\\Events\\MensagemEnviada" => 'receberMensagem',
        ];
    }

    public function mount(): void
    {
        $user = auth()->user();
        $this->salas = $user->salas()->get();
    }

    /**
     * Este método é chamado quando uma nova mensagem é recebida pelo broadcast.
     */
    public function receberMensagem(array $data): void
    {
        // Encontra a mensagem completa na base de dados a partir dos dados recebidos
        $novaMsg = Mensagem::find($data['mensagem']['id']);

        // Adiciona a nova mensagem à lista, APENAS se não for do próprio utilizador
        // (para evitar duplicados, já que adicionamos a nossa própria mensagem instantaneamente)
        if (auth()->id() != $novaMsg->remetente_id) {
            $this->mensagens->push($novaMsg);
            $this->dispatch('mensagemAdicionada'); // Dispara o evento para o scroll do JS
        }
    }

    public function selecionarSala(int $salaId): void
    {
        $this->salaSelecionada = Sala::findOrFail($salaId);
        $this->mensagens = $this->salaSelecionada->mensagens()->get();
        // Dispara o evento de scroll para o JS ir para o fundo ao selecionar a sala
        $this->dispatch('mensagemAdicionada');
    }

    public function enviarMensagem(): void
    {
        if (!$this->salaSelecionada || empty(trim($this->novaMensagem))) {
            return;
        }

        $mensagem = $this->salaSelecionada->mensagens()->create([
            'remetente_id' => auth()->id(),
            'conteudo' => $this->novaMensagem,
        ]);

        $this->reset('novaMensagem');

        // Adiciona a nossa própria mensagem à lista para um feedback instantâneo
        $this->mensagens->push($mensagem);

        // Transmite o evento para os outros utilizadores
        broadcast(new MensagemEnviada($mensagem))->toOthers();

        // Dispara o evento de scroll para a nossa própria mensagem
        $this->dispatch('mensagemAdicionada');
    }

    public function mostrarModalNovaSala(): void
    {
        $this->reset('nomeNovaSala');
        $this->mostrandoModalNovaSala = true;
    }

    public function fecharModalNovaSala(): void
    {
        $this->mostrandoModalNovaSala = false;
    }

    public function criarNovaSala(): void
    {
        $validated = $this->validate([
            'nomeNovaSala' => 'required|string|min:3|max:50',
        ]);

        $sala = Sala::create([
            'nome' => $validated['nomeNovaSala'],
            'criado_por_utilizador_id' => auth()->id(),
        ]);

        $sala->utilizadores()->attach(auth()->id());
        $this->salas = auth()->user()->salas()->get();
        $this->fecharModalNovaSala();
        $this->dispatch('notify', message: 'Sala criada com sucesso!');
    }

    public function render(): View
    {
        return view('livewire.chat.chat-index');
    }
}
