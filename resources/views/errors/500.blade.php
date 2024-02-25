<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Experior - {{ $title ?? 'Welcome' }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:weight@400;600;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</head>

<body class="font-sans antialiased w-full bg-main">
    <main class="flex w-full min-h-screen">
        <div class="w-full px-0 mb-8 pb-6">
            <div class="w-full p-8 md:p-6 md:rounded-l-lg h-full flex items-center justify-center">
                <div class="flex flex-col items-center">
                    <img src="/logo.png" class="w-64" />
                    <h2 class="mt-12 text-white font-bold text-4xl">Ops! Something went REALLY wrong!</h2>
                    <div class="text-white text-3xl mt-4">And the fault is all ours</div>
                    <div class="text-white text-xl mt-4">Don't worry, we're figuring out what happened</div>
                </div>
            </div>
        </div>
        @include('components.footer')
    </main>
</body>

</html>
