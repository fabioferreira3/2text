<div class="h-[250px]">
    <div class="relative h-full group">
        <img class="rounded-t-xl w-full h-full object-cover"
            src={{ $image ?? '/images/placeholder-social-media.jpg' }} />
        <div class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
            <div class="z-20 flex flex-col gap-2">
                <button wire:click="toggleImageGenerator"
                    class="text-white bg-secondary px-2 py-1 rounded-lg flex items-center gap-2">
                    <x-icon name="refresh" class="w-5 h-5" />
                    <span>Regenerate</span></button>
                <button wire:click="downloadImage"
                    class="border border-white border-zinc-600 text-white font-medium bg-main px-3 py-1 rounded-lg flex items-center gap-2">
                    <x-icon name="arrow-circle-down" class="w-5 h-5" />
                    <span>Download</span>
                </button>
            </div>
        </div>
        <div
            class="group-hover:opacity-60 absolute flex items-center justify-center inset-0 bg-black rounded-t-xl opacity-0 transition-opacity duration-300 ease-in-out">
        </div>
    </div>
</div>
