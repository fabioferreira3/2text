<div id="modal" class="fixed inset-0 w-full z-100 bg-black bg-opacity-30 flex items-center justify-center backdrop-blur-sm">
    <div class="relative bg-white overflow-auto rounded-lg shadow-lg w-full sm:w-4/5 {{ $sizeConstraints }} max-h-screen">
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>