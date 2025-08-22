<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Sala;
use App\Models\Mensagem;
use App\Models\User;
use App\Events\MensagemEnviada;
use App\Events\MensagemDiretaEnviada;
use Livewire\Attributes\On;

class ChatIndex extends Component
{
    public bool $mostrandoModalNovaSala = false;
    public string $nomeNovaSala = '';
    public $salas;
    public $utilizadores;
    public ?Sala $salaSelecionada = null;
    public ?User $destinatario = null;
    public $mensagens = [];
    public string $novaMensagem = '';

    #[On('membrosAtualizados')]
    public function carregarDadosIniciais(): void
    {
        $user = auth()->user()->fresh(); // <-- Adicione o ->fresh()
        if ($user) {
            $this->salas = $user->salas()->get();
            $this->utilizadores = User::where('id', '!=', $user->id)->get();
        }
    }

    public function getListeners()
    {
        $userId = auth()->id();
        if (!$userId) return [];

        $listeners = [
            "echo-private:utilizador.{$userId},.App\\Events\\MensagemDiretaEnviada" => 'receberMensagemDireta',
        ];

        if ($this->salaSelecionada) {
            $listeners["echo-private:sala.{$this->salaSelecionada->id},.App\\Events\\MensagemEnviada"] = 'receberMensagem';
        }

        // Adiciona o ouvinte do #[On] ao array
        $listeners = array_merge($listeners, $this->getOnListeners());

        return $listeners;
    }

    // Este método é um helper para o getListeners(), não precisa de mexer aqui.
    protected function getOnListeners()
    {
        $listeners = [];
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(On::class) as $attribute) {
                $listeners[$attribute->getArguments()[0]] = $method->getName();
            }
        }
        return $listeners;
    }

    public function mount(): void
    {
        if (!auth()->check()) {
            redirect()->route('login');
            return;
        }
        $this->carregarDadosIniciais();
    }

    public function render(): View
    {
        return view('livewire.chat.chat-index');
    }

    public function receberMensagem(array $data): void
    {
        $novaMsg = Mensagem::find($data['mensagem']['id']);
        if ($this->salaSelecionada && $this->salaSelecionada->id == $novaMsg->sala_id) {
            if (auth()->id() != $novaMsg->remetente_id) {
                $this->mensagens->push($novaMsg);
                $this->dispatch('mensagemAdicionada');
            }
        }
    }

    public function receberMensagemDireta(array $data): void
    {
        $novaMsg = Mensagem::find($data['mensagem']['id']);
        if ($this->destinatario && $this->destinatario->id == $novaMsg->remetente_id) {
            $this->mensagens->push($novaMsg);
            $this->dispatch('mensagemAdicionada');
        }
    }

    public function selecionarSala(int $salaId): void
    {
        // Esta linha estava em falta:
        $this->salaSelecionada = Sala::findOrFail($salaId);

        $this->destinatario = null;
        $this->mensagens = $this->salaSelecionada->mensagens()->get();

        // Marcar a sala como lida
        auth()->user()->salas()->updateExistingPivot($salaId, [
            'last_read_at' => now()
        ]);

        $this->dispatch('mensagemAdicionada'); // Para o scroll
    }

    public function iniciarConversa(int $userId): void
    {
        $this->destinatario = User::findOrFail($userId);
        $this->salaSelecionada = null;
        $this->mensagens = Mensagem::where(fn($q) => $q->where('remetente_id', auth()->id())->where('destinatario_id', $userId))
            ->orWhere(fn($q) => $q->where('remetente_id', $userId)->where('destinatario_id', auth()->id()))
            ->orderBy('created_at')->get();
        $this->dispatch('mensagemAdicionada');
    }

    public function enviarMensagem(): void
    {
        if (empty(trim($this->novaMensagem))) return;
        $mensagem = null;
        if ($this->salaSelecionada) {
            $mensagem = $this->salaSelecionada->mensagens()->create(['remetente_id' => auth()->id(), 'conteudo' => $this->novaMensagem]);
            broadcast(new MensagemEnviada($mensagem))->toOthers();
        } elseif ($this->destinatario) {
            $mensagem = Mensagem::create(['remetente_id' => auth()->id(), 'destinatario_id' => $this->destinatario->id, 'conteudo' => $this->novaMensagem]);
            broadcast(new MensagemDiretaEnviada($mensagem))->toOthers();
        }

        if ($mensagem) {
            $this->reset('novaMensagem');
            $this->mensagens->push($mensagem);
            $this->dispatch('mensagemAdicionada');
        }
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
        $validated = $this->validate(['nomeNovaSala' => 'required|string|min:3|max:50']);
        $sala = Sala::create(['nome' => $validated['nomeNovaSala'], 'criado_por_utilizador_id' => auth()->id()]);
        $sala->utilizadores()->attach(auth()->id());
        $this->carregarDadosIniciais();
        $this->fecharModalNovaSala();
        $this->dispatch('notify', message: 'Sala criada com sucesso!');
    }

    public function apagarSala(int $salaId): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $sala = Sala::findOrFail($salaId);
        $sala->delete();

        if ($this->salaSelecionada && $this->salaSelecionada->id === $salaId) {
            $this->salaSelecionada = null;
            $this->mensagens = [];
        }
        $this->carregarDadosIniciais();
        $this->dispatch('notify', message: 'Sala apagada com sucesso!');
    }

    /**
     * Propriedade computada para contar mensagens não lidas.
     */
    public function getSalasComNaoLidasProperty()
    {
        $salasComContagem = [];
        foreach ($this->salas as $sala) {
            $lastRead = $sala->pivot->last_read_at ?? now()->subYears(10);

            $count = Mensagem::where('sala_id', $sala->id)
                ->where('created_at', '>', $lastRead)
                ->where('remetente_id', '!=', auth()->id()) // Não contar as nossas próprias
                ->count();

            $salasComContagem[$sala->id] = $count;
        }
        return $salasComContagem;
    }
}
