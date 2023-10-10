<div
    class="invisible opacity-0 group-hover/block:opacity-100 group-hover/block:visible transition-display duration-200 flex items-center flex-wrap justify-between bg-gray-200 border border-gray-200 p-2 w-full text-sm gap-1 right-0 top-full absolute rounded-lg border-t z-50">
    <div class="flex items-center gap-1">
        <button wire:click="copy"
            class="flex items-center text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="clipboard-copy" width="18" height="18" />
        </button>
        <button wire:click="delete"
            class="flex items-center text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="trash" width="18" height="18" />
        </button>
    </div>
    <div class="flex items-center gap-1">
        <button
            class="flex items-center gap-1 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150"
            wire:click="toggleCustomPrompt">
            <x-icon name="speakerphone" width="18" height="18" />
            <div>Ask to...</div>
        </button>
        <button wire:click="shorten"
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="menu-alt-4" width="18" height="18" />
            <div>shorten</div>
        </button>
        <button wire:click="expand"
            class="flex items-center gap-1 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="menu" width="18" height="18" />
            <div>expand</div>
        </button>
        <button
            class="relative flex items-center gap-1 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-white border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="sparkles" width="18" height="18" />
            <div>more</div>
            <div class="absolute right-0 bg-black top-full w-[150px]">eita</div>
        </button>
    </div>

</div>
