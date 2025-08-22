<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="h-screen overflow-hidden font-sans antialiased">
        <x-banner />

        <div class="flex h-full flex-col">
            <!-- Cabeçalho Fixo -->
            <header class="flex-shrink-0">
                @livewire('navigation-menu')
            </header>

            <!-- Conteúdo Principal -->
            <div class="flex-1 flex flex-row overflow-hidden">
                <!-- Conteúdo que será preenchido pelo nosso componente Livewire -->
                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>