<div class="flex h-screen antialiased text-gray-800">
    <div class="flex flex-row h-full w-full overflow-x-hidden">

        <!-- Sidebar -->
        <div class="flex flex-col py-6 pl-6 pr-2 w-72 bg-white border-r flex-shrink-0">
            <!-- Logo / Título -->
            <div class="flex items-center justify-center h-12 w-full">
                <div class="font-bold text-2xl text-gray-800">Nosso Chat</div>
            </div>

            <!-- Secção: Salas -->
            <div class="flex flex-col mt-8">
                <div class="flex flex-row items-center justify-between text-xs uppercase tracking-wide text-gray-500">
                    <span class="font-bold">Salas</span>
                    <span class="flex items-center justify-center bg-gray-200 h-4 w-4 rounded-full text-gray-600 text-[10px]">
                        {{ $salas->count() }}
                    </span>
                </div>
                <div class="flex flex-col space-y-1 mt-4 -mx-2 h-auto overflow-y-auto">
                    @forelse ($salas as $sala)
                        <button wire:click="selecionarSala({{ $sala->id }})"
                            class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2 transition @if ($salaSelecionada && $salaSelecionada->id == $sala->id) bg-blue-50 border-l-4 border-blue-400 @endif">
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
        </div>

        <!-- Área do Chat -->
        <div class="flex flex-col flex-auto h-full p-6 bg-gray-50">
            <div class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-white border h-full p-4">

                @if ($salaSelecionada)
                    <!-- Cabeçalho -->
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $salaSelecionada->nome }}</h2>
                    </div>

                    <!-- Lista de Mensagens -->
                    <div class="flex flex-col h-full overflow-x-auto mb-4">
                        <div class="flex flex-col h-full">
                            <div class="flex flex-col space-y-4 p-4">
                                @forelse ($mensagens as $mensagem)
                                    @if ($mensagem->remetente_id == auth()->id())
                                        <!-- Minha mensagem -->
                                        <div class="flex items-end justify-end">
                                            <div class="flex flex-col items-end">
                                                <div
                                                    class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-100 text-gray-800 border border-blue-200">
                                                    {{ $mensagem->conteudo }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Mensagem de outro -->
                                        <div class="flex items-end">
                                            <div class="flex flex-col items-start">
                                                <div
                                                    class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-100 text-gray-800 border border-gray-200">
                                                    {{ $mensagem->conteudo }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-center text-gray-400">
                                        Nenhuma mensagem ainda.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="flex flex-row items-center h-16 rounded-xl bg-gray-50 w-full px-4 border-t">
                        <div class="flex-grow">
                            <input
                                type="text"
                                wire:model="novaMensagem"
                                wire:keydown.enter="enviarMensagem"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 text-sm px-3 py-2"
                                placeholder="Escreva a sua mensagem..."
                            />
                        </div>
                        <div class="ml-4">
                            <button
                                wire:click="enviarMensagem"
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
</div>
