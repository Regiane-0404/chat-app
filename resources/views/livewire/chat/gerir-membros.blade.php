<div class="p-6 max-w-4xl mx-auto">
    <!-- Cabeçalho da Página -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gerir Membros</h1>
            <p class="text-gray-500">Sala: <span class="font-semibold">{{ $sala->nome }}</span></p>
        </div>
        <a href="{{ route('chat.index') }}" wire:navigate
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition text-sm font-medium">
            &larr; Voltar ao Chat
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Lista de Membros Atuais -->
        <div class="mt-4">
            <h4 class="text-sm font-bold text-gray-600 uppercase">Membros Atuais ({{ $sala->utilizadores->count() }})
            </h4>
            <div class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                @forelse($sala->utilizadores as $membro)
                    <div class="flex items-center justify-between p-2 border-b border-gray-200">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                {{ strtoupper(substr($membro->name, 0, 1)) }}
                            </div>
                            <span class="ml-3 text-sm font-medium">{{ $membro->name }}</span>
                        </div>
                        <button wire:click="confirmarRemocao({{ $membro->id }})"
                            class="text-red-500 hover:text-red-700 text-xs font-semibold">
                            Remover
                        </button>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Nenhum membro encontrado.</p>
                @endforelse
            </div>
        </div>

        <!-- Formulário para Convidar Novos Membros -->
        <div class="mt-8 border-t pt-6">
            <h4 class="text-sm font-bold text-gray-600 uppercase">Convidar Novo Membro</h4>
            <div class="mt-2">
                <input type="text" wire:model.live="termoPesquisa" wire:keyup="pesquisarUtilizadores"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 text-sm px-3 py-2"
                    placeholder="Escreva um nome para procurar..." />

                @if (count($resultadosPesquisa) > 0)
                    <div class="mt-2 space-y-2 border rounded-lg p-2">
                        @foreach ($resultadosPesquisa as $utilizador)
                            <div class="flex items-center justify-between p-2">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">
                                        {{ strtoupper(substr($utilizador->name, 0, 1)) }}
                                    </div>
                                    <span class="ml-3 text-sm font-medium">{{ $utilizador->name }}</span>
                                </div>
                                <button wire:click="adicionarMembro({{ $utilizador->id }})"
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-xs font-medium">
                                    Adicionar
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Remoção -->
    @if ($membroParaRemover)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                <h3 class="text-lg font-medium">Confirmar Remoção</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Tem a certeza que quer remover <strong>{{ $membroParaRemover->name }}</strong> da sala?
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="cancelarRemocao"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                    <button wire:click="removerMembroConfirmado"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Sim, Remover</button>
                </div>
            </div>
        </div>
    @endif
</div>
