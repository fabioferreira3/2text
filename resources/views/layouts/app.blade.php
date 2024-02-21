<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Experior AI - {{ $title ?? 'Welcome' }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:weight@400;600;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

<body class="font-sans antialiased bg-main overflow-hidden">
    <x-notifications />
    <x-banner />
    @livewire('common.notifications')
    <main class="flex w-full h-screen">
        <!-- Sidebar -->
        <div class="hidden fixed top-0 left-0 w-full md:w-[250px] sm:block h-screen p-6 bg-main overflow-y-hidden">
            @livewire('common.sidebar')
        </div>
        <!-- End: Sidebar -->

        <div class="flex flex-col w-full md:ml-[250px] h-screen px-0 overflow-hidden bg-white pb-8">
            <!-- Header -->
            <header class="sticky top-0">
                <div class="z-50">
                    @livewire('navigation-menu')
                </div>
                <div class='h-[1px]'>
                    <div class='h-full bg-gray-200 rounded-lg'></div>
                </div>
                @hasSection('header')
                <div class="p-6 border-b border-zinc-200">
                    @yield('header')
                </div>
                @endif
            </header>
            <!-- End: Header -->

            <div class="p-6 bg-white grow overflow-auto">
                {{ $slot }}
            </div>
        </div>
        {{-- @if(app()->env === 'local')
        @livewire('chat')
        @endif --}}
        @include('components.footer')
    </main>

    @livewireScriptConfig
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    @vite(['resources/js/typewriter.js'])
    @stack('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.addEventListener('alert', ({
            detail: {
                type,
                message
            }
        }) => {
            Toast.fire({
                icon: type,
                title: message
            })
        })

        window.addEventListener('refresh-page', () => {
            window.location.reload();
        });

        document.addEventListener('livewire:init', function() {
            window.livewire.on('addToClipboard', function(message) {
                navigator.clipboard.writeText(message);
            });
            window.livewire.on('openLinkInNewTab', link => {
                window.open(link, '_blank');
            });
        });

        // window.livewire.on('openLinkInNewTab', link => {
        //     window.open(link, '_blank');
        // });

        // Text block height adjustment

        function adjustTextArea() {
            let textareas = document.querySelectorAll('.autoExpandTextarea');

            function adjustHeight(el) {
                el.style.height = 'auto';
                el.style.height = el.scrollHeight + 'px';
            }

            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    adjustHeight(this);
                });

                // Initial adjustment
                adjustHeight(textarea);
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            adjustTextArea();
        });

        document.addEventListener('adjustTextArea', function() {
            adjustTextArea();
        });
    </script>
</body>

</html>