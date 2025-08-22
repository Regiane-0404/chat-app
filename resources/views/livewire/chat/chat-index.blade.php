<!-- Container Principal: controla tudo -->
<div class="h-screen overflow-hidden flex flex-col font-sans antialiased text-gray-800">

    <!-- Barra de Navegação Superior Fixa -->
    <header class="flex-shrink-0 z-20 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo e Título -->
                <div class="flex items-center">
                    <a href="{{ route('chat.index') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                    <div class="font-bold text-xl text-gray-800 ml-4">Chat App</div>
                </div>

                <!-- Menu do Utilizador -->
                @auth
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                                    <div>{{ Auth::user()->name }} @if (Auth::user()->isAdmin())
                                            (ADMIN)
                                        @endif
                                    </div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                        @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal: ocupa o resto -->
    <div class="flex-1 flex flex-row overflow-hidden">
        <!-- Sidebar: Salas -->
        <div class="flex flex-col py-6 pl-6 pr-2 w-72 bg-white border-r flex-shrink-0">
            <div class="flex flex-row items-center justify-between text-xs uppercase tracking-wide text-gray-500 px-1">
                <span class="font-bold">Salas</span>
                @if (auth()->user()->isAdmin())
                    <button wire:click="mostrarModalNovaSala"
                        class="flex items-center justify-center h-5 w-5 bg-gray-200 text-gray-600 rounded-full hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="font-bold text-sm">+</span>
                    </button>
                @endif
            </div>
            <div class="flex flex-col space-y-1 mt-4 -mx-2 px-1 overflow-y-auto">
                @forelse ($salas as $sala)
                    <div class="flex items-center justify-between hover:bg-gray-100 rounded-xl p-2 group">
                        <div class="flex items-center justify-between w-full">
                            <button wire:key="sala-{{ $sala->id }}" wire:click="selecionarSala({{ $sala->id }})"
                                class="flex-1 flex items-center text-left">
                                <img src="{{ $sala->avatar_url }}" alt="{{ $sala->nome }}"
                                    class="h-8 w-8 rounded-full object-cover">
                                <div class="ml-2 text-sm font-medium text-gray-800">{{ $sala->nome }}</div>
                            </button>
                            @php($naoLidas = $this->salasComNaoLidas[$sala->id] ?? 0)
                            @if ($naoLidas > 0)
                                <span
                                    class="ml-2 inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1.5 
                                             text-xs font-semibold rounded-full bg-red-500 text-white">
                                    {{ $naoLidas > 99 ? '99+' : $naoLidas }}
                                </span>
                            @endif
                        </div>
                        @if (auth()->user()->isAdmin())
                            <button wire:click="apagarSala({{ $sala->id }})"
                                wire:confirm="Tem a certeza que quer apagar esta sala e todas as suas mensagens?"
                                class="text-red-500 ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="p-2 text-sm text-gray-500">Nenhuma sala criada.</p>
                @endforelse
            </div>

            <!-- Secção: Conversas Diretas -->
            <div class="flex flex-col mt-8">
                <div
                    class="flex flex-row items-center justify-between text-xs uppercase tracking-wide text-gray-500 px-1">
                    <span class="font-bold">Utilizadores</span>
                </div>
                <div class="flex flex-col space-y-1 mt-4 -mx-2 px-1 overflow-y-auto">
                    @forelse ($utilizadores as $utilizador)
                        <button wire:click="iniciarConversa({{ $utilizador->id }})"
                            class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2 transition
                            {{ $destinatario && $destinatario->id == $utilizador->id ? 'bg-blue-50 border-l-4 border-blue-400' : '' }}">
                            <img src="{{ $utilizador->profile_photo_url }}" alt="{{ $utilizador->name }}"
                                class="h-8 w-8 rounded-full object-cover">
                            <div class="ml-2 text-sm font-medium text-gray-800">{{ $utilizador->name }}</div>
                        </button>
                    @empty
                        <p class="p-2 text-sm text-gray-500">Nenhum outro utilizador encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Área do Chat -->
        <div class="flex flex-col flex-auto h-full p-6 bg-gray-50">
            <div class="flex flex-col flex-auto rounded-2xl bg-white border h-full">

                @if ($salaSelecionada || $destinatario)
                    <!-- Cabeçalho fixo -->
                    <div class="flex items-center justify-between p-4 border-b flex-shrink-0">
                        <h2 class="text-xl font-semibold text-gray-800">
                            @if ($salaSelecionada)
                                {{ $salaSelecionada->nome }}
                            @elseif ($destinatario)
                                {{ $destinatario->name }}
                            @endif
                        </h2>

                        <!-- Link para gerir membros (só para admin) -->
                        @if ($salaSelecionada && auth()->user()->isAdmin())
                            <a href="{{ route('salas.membros', $salaSelecionada) }}" wire:navigate
                                class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.122-1.28-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.122-1.28.356-1.857m0 0a3.002 3.002 0 012.386-2.043M10 14a3 3 0 110-6 3 3 0 010 6z" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    <!-- Lista de Mensagens com scroll interno -->
                    <div id="lista-mensagens" x-data x-init="const container = $el;
                    container.scrollTop = container.scrollHeight;
                    Livewire.on('mensagemAdicionada', () => { setTimeout(() => { container.scrollTop = container.scrollHeight; }, 50); });"
                        class="flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse ($mensagens as $mensagem)
                            @if ($mensagem->remetente_id == auth()->id())
                                <!-- Minha mensagem -->
                                <div class="flex items-end justify-end">
                                    <div class="flex flex-col items-end">
                                        <div
                                            class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-100 text-gray-800 border border-blue-200">
                                            {{ $mensagem->conteudo }}
                                        </div>
                                        <span class="text-xs text-gray-400 mt-1">
                                            {{ $mensagem->created_at->format('H:i') }}
                                        </span>
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
                                        <span class="text-xs text-gray-400 mt-1">
                                            {{ $mensagem->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-gray-400 h-full flex items-center justify-center">Nenhuma
                                mensagem ainda.</div>
                        @endforelse
                    </div>

                    <!-- Input de Envio -->
                    <div class="flex items-center h-16 p-4 border-t bg-gray-50 flex-shrink-0">
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
                        <p>Selecione uma sala ou um utilizador para começar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Criação de Sala -->
    @if ($mostrandoModalNovaSala)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background-color: rgba(0, 0, 0, 0.5);" wire:click.self="fecharModalNovaSala">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto"
                @click.away="$wire.fecharModalNovaSala()" x-data x-init="$nextTick(() => document.getElementById('nomeNovaSala')?.focus())">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800">Criar Nova Sala</h3>
                    <div class="mt-4">
                        <div>
                            <label for="nomeNovaSala" class="block text-sm font-medium text-gray-700">Nome da
                                Sala</label>
                            <input type="text" id="nomeNovaSala" wire:model.defer="nomeNovaSala"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                placeholder="Ex: Projeto Marketing">
                            @error('nomeNovaSala')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="fecharModalNovaSala"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Cancelar</button>
                        <button type="button" wire:click="criarNovaSala"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Criar
                            Sala</button>
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
        class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
        x-init="init()">
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
                    this.message = data[0].message;
                    this.show = true;
                    setTimeout(() => this.show = false, 3000);
                });
            }
        }
    }
</script>
