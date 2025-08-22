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
        <!-- Secção de Avatar -->
        <div>
            <h4 class="text-sm font-bold text-gray-600 uppercase">Avatar da Sala</h4>
            <div class="mt-2 flex items-center space-x-4">

                <!-- Avatar Atual -->
                <img src="{{ $sala->avatar_url }}" alt="Avatar da Sala"
                    class="h-16 w-16 rounded-full object-cover flex-shrink-0">

                <!-- Formulário de Upload -->
                <form wire:submit.prevent="salvarAvatar" class="flex-1">
                    <div class="flex flex-col space-y-2">

                        <!-- Linha 1: Upload + Salvar -->
                        <div class="flex items-center space-x-2">
                            <!-- Input oculto -->
                            <input type="file" wire:model="novoAvatar" id="novoAvatar-{{ $sala->id }}"
                                class="hidden" accept="image/*">

                            <!-- Botão Selecionar -->
                            <label for="novoAvatar-{{ $sala->id }}"
                                class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 cursor-pointer transition text-sm font-medium flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span>Selecionar</span>
                            </label>

                            <!-- Botão Salvar -->
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="novoAvatar">Salvar</span>
                                <span wire:loading wire:target="novoAvatar">A guardar...</span>
                            </button>
                        </div>

                        <!-- Nome do ficheiro -->
                        @if ($novoAvatar)
                            <div class="text-xs text-gray-600 mt-1">
                                ✅ {{ $novoAvatar->getClientOriginalName() }}
                                ({{ number_format($novoAvatar->getSize() / 1024, 2) }} KB)
                            </div>
                        @endif

                        <!-- Feedback de carregamento -->
                        <div wire:loading wire:target="novoAvatar" class="text-xs text-blue-500 mt-1">
                            A carregar...
                        </div>

                        <!-- Erro de validação -->
                        @error('novoAvatar')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Membros Atuais -->
        <div class="mt-6 border-t pt-6">
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
