<div size="small" id="modal"
    class="fixed overflow-auto inset-0 w-full z-20 bg-black bg-opacity-30 flex items-center justify-center backdrop-blur-sm">
    <div
        class="relative bg-white rounded-lg shadow-lg w-full sm:w-4/5 {{ $sizeConstraints }} max-h-[800px] overflow-y-auto">
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>
