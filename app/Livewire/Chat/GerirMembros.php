<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Sala;
use App\Models\User;
use Livewire\WithFileUploads; // CORREÇÃO 1: Importar o trait com o namespace completo

class GerirMembros extends Component
{
    use WithFileUploads; // CORREÇÃO 2: Usar o trait aqui dentro da classe

    public Sala $sala;
    public string $termoPesquisa = '';
    public $resultadosPesquisa = [];
    public ?User $membroParaRemover = null;
    public $novoAvatar;

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
        if (strlen($this->termoPesquisa) < 2) {
            $this->resultadosPesquisa = [];
            return;
        }
        $membrosAtuaisIds = $this->sala->utilizadores->pluck('id');
        $this->resultadosPesquisa = User::where('name', 'like', '%' . $this->termoPesquisa . '%')
            ->whereNotIn('id', $membrosAtuaisIds)->take(5)->get();
    }

    public function adicionarMembro(int $userId): void
    {
        $this->sala->utilizadores()->attach($userId);
        $this->sala->load('utilizadores');
        $this->dispatch('notify', message: 'Membro adicionado com sucesso!');
        $this->dispatch('membrosAtualizados');
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
        $this->dispatch('membrosAtualizados');
        $this->cancelarRemocao();
    }

    public function salvarAvatar(): void
    {
        $this->validate([
            'novoAvatar' => 'required|image|max:2048',
        ]);

        $path = $this->novoAvatar->store('sala-avatars', 'public');

        $this->sala->update(['avatar_path' => $path]);

        $this->reset('novoAvatar');
        $this->dispatch('notify', message: 'Avatar da sala atualizado!');
    }
}
