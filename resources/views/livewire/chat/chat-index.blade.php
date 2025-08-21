<div class="relative flex h-full max-h-screen w-full antialiased text-gray-800">
    <!-- Container principal -->
    <div class="flex flex-row h-full w-full">

        <!-- Sidebar: Salas -->
        <div class="flex flex-col py-6 pl-6 pr-2 w-72 bg-white border-r flex-shrink-0">
            <!-- Logo -->
            <div class="flex items-center justify-center h-12 w-full">
                <div class="font-bold text-2xl text-gray-800">Nosso Chat</div>
            </div>

            <!-- Cabeçalho das Salas -->
            <div
                class="flex flex-row items-center justify-between text-xs uppercase tracking-wide text-gray-500 mt-4 px-1">
                <span class="font-bold">Salas</span>
                <button wire:click="mostrarModalNovaSala"
                    class="flex items-center justify-center h-5 w-5 bg-gray-200 text-gray-600 rounded-full hover:bg-gray-300 hover:text-gray-800 transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <span class="font-bold text-sm">+</span>
                </button>
            </div>

            <!-- Lista de Salas -->
            <div class="flex flex-col space-y-1 mt-4 -mx-2 h-auto overflow-y-auto px-1">
                @forelse ($salas as $sala)
                    <button wire:key="sala-{{ $sala->id }}" wire:click="selecionarSala({{ $sala->id }})"
                        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2 transition
                        {{ $salaSelecionada && $salaSelecionada->id == $sala->id ? 'bg-blue-50 border-l-4 border-blue-400' : '' }}">
                        <div
                            class="flex items-center justify-center h-8 w-8 bg-blue-100 rounded-full text-blue-600 font-bold">
                            {{ strtoupper(substr($sala->nome, 0, 1)) }}
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-800">{{ $sala->nome }}</div>
                    </button>
                @empty
                    <p class="p-2 text-sm text-gray-500">Nenhuma sala criada.</p>
                @endforelse
            </div>
        </div>

        <!-- Área do Chat -->
              <!-- Área do Chat -->
        <div class="flex flex-col flex-auto h-full max-h-screen p-6 bg-gray-50">
            <div class="flex flex-col flex-auto rounded-2xl bg-white border h-full p-4">

                @if ($salaSelecionada)
                    <!-- Cabeçalho fixo -->
                    <div class="sticky top-0 z-10 bg-white border-b p-4">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $salaSelecionada->nome }}</h2>
                    </div>

                    <!-- Lista de Mensagens -->
                    <div id="lista-mensagens" x-data x-init="const container = $el;
                    container.scrollTop = container.scrollHeight;
                    Livewire.on('mensagemAdicionada', () => {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 50);
                    });"
                        class="flex flex-col h-full overflow-y-auto p-4 space-y-4">
                        @forelse ($mensagens as $mensagem)
                            @if ($mensagem->remetente_id == auth()->id())
                                <!-- Minha mensagem -->
                                <div class="flex justify-end">
                                    <div
                                        class="max-w-xs bg-blue-100 text-gray-800 border border-blue-200 rounded-lg rounded-br-none px-4 py-2">
                                        {{ $mensagem->conteudo }}
                                    </div>
                                </div>
                            @else
                                <!-- Mensagem de outro -->
                                <div class="flex justify-start">
                                    <div
                                        class="max-w-xs bg-gray-100 text-gray-800 border border-gray-200 rounded-lg rounded-bl-none px-4 py-2">
                                        {{ $mensagem->conteudo }}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-gray-400">Nenhuma mensagem ainda.</div>
                        @endforelse
                    </div>

                    <!-- Input de Envio -->
                    <div class="flex flex-row items-center h-16 rounded-xl bg-gray-50 px-4 border-t">
                        <div class="flex-grow">
                            <input type="text" wire:model.defer="novaMensagem" wire:keydown.enter="enviarMensagem"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 text-sm px-3 py-2"
                                placeholder="Escreva a sua mensagem..." />
                        </div>
                        <div class="ml-4">
                            <button wire:click="enviarMensagem"
                                class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-sm shadow-sm transition">
                                Enviar
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Estado inicial -->
                    <div class="flex flex-col h-full justify-center items-center text-gray-400">
                        <p>Selecione uma sala para começar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Criação de Sala -->
    @if ($mostrandoModalNovaSala)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            style="background-color: rgba(0, 0, 0, 0.5);" wire:click.self="fecharModalNovaSala">
            <div class="pointer-events-auto bg-white rounded-lg shadow-xl w-full max-w-md mx-auto"
                @click.away="$wire.fecharModalNovaSala()" x-data x-init="$nextTick(() => document.getElementById('nomeNovaSala')?.focus())">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800">Criar Nova Sala</h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="nomeNovaSala" class="block text-sm font-medium text-gray-700">Nome da
                                Sala</label>
                            <input type="text" id="nomeNovaSala" wire:model.defer="nomeNovaSala"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Ex: Projeto Marketing">
                            @error('nomeNovaSala')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="fecharModalNovaSala"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition text-sm font-medium">
                            Cancelar
                        </button>
                        <button type="button" wire:click="criarNovaSala"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm font-medium">
                            Criar Sala
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast de Sucesso -->
    <div x-data="toast()" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" x-init="init()">
        <span x-text="message"></span>
    </div>
</div>

<script>
    function toast() {
        return {
            show: false,
            message: '',
            init() {
                Livewire.on('notify', (data) => {
                    this.message = data.message;
                    this.show = true;
                    setTimeout(() => this.show = false, 3000);
                });
            }
        }
    }
</script>
