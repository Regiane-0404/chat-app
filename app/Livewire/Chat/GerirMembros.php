<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Sala;
use App\Models\User;

class GerirMembros extends Component
{
    public Sala $sala;
    public string $termoPesquisa = '';
    public $resultadosPesquisa = [];
    public ?User $membroParaRemover = null;

    public function mount(Sala $sala): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }
        $this->sala = $sala->load('utilizadores');
    }

    public function render(): View
    {
        return view('livewire.chat.gerir-membros');
    }

    public function pesquisarUtilizadores(): void
    {
        if (strlen($this->termoPesquisa) < 2) { $this->resultadosPesquisa = []; return; }
        $membrosAtuaisIds = $this->sala->utilizadores->pluck('id');
        $this->resultadosPesquisa = User::where('name', 'like', '%' . $this->termoPesquisa . '%')
            ->whereNotIn('id', $membrosAtuaisIds)->take(5)->get();
    }

    public function adicionarMembro(int $userId): void
    {
        $this->sala->utilizadores()->attach($userId);
        $this->sala->load('utilizadores');
        $this->dispatch('notify', message: 'Membro adicionado com sucesso!');
        $this->dispatch('membrosAtualizados'); // Dispara o evento global
        $this->reset('termoPesquisa', 'resultadosPesquisa');
    }

    public function confirmarRemocao(int $userId): void
    {
        $this->membroParaRemover = User::find($userId);
    }

    public function cancelarRemocao(): void
    {
        $this->reset('membroParaRemover');
    }

    public function removerMembroConfirmado(): void
    {
        if (!$this->membroParaRemover) return;
        $userId = $this->membroParaRemover->id;

        if ($userId === $this->sala->criado_por_utilizador_id || $userId === 1) {
            $this->dispatch('notify', message: 'Não pode remover o proprietário.');
            $this->cancelarRemocao();
            return;
        }

        $this->sala->utilizadores()->detach($userId);
        $this->sala->load('utilizadores');
        $this->dispatch('notify', message: 'Membro removido com sucesso!');
        $this->dispatch('membrosAtualizados'); // Dispara o evento global
        $this->cancelarRemocao();
    }
}