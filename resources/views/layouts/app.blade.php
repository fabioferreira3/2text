<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>2Text AI</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @wireUiScripts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="flex min-h-screen bg-gray-100 relative">
            <div class="w-1/4 h-full fixed p-6 bg-white border-r-2">
                @include('livewire.common.sidebar')
            </div>
            <!-- Page Content -->
            <main class="mx-auto w-3/4 absolute right-0 min-h-screen bg-white pl-6">
                @livewire('navigation-menu')
                <div class="p-6 rounded-lg bg-zinc-100">
                    {{ $slot }}
                </div>
            </main>
            {{-- @livewire('navigation-menu') --}}

        </div>

        @stack('modals')

        @livewireScripts
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>
