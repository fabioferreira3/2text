<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-main">
    <div>
        {{ $logo }}
    </div>

    <div class="flex w-full justify-center mt-6 gap-8">
        <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
