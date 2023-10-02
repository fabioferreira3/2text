<div
    class="flex items-center justify-between w-full text-sm gap-1 absolute right-0 -top-8 bg-red-100 rounded-lg border-t">
    <div class="flex items-center gap-1">
        <button wire:click="copy"
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="clipboard-copy" width="18" height="18" />
            <div>copy</div>
        </button>
        <button wire:click="delete"
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="trash" width="18" height="18" />
            <div>delete</div>
        </button>
    </div>
    <div class="flex items-center gap-1">
        <button wire:click="shorten"
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="menu-alt-4" width="18" height="18" />
            <div>shorten</div>
        </button>
        <button wire:click="expand"
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
            <x-icon name="menu" width="18" height="18" />
            <div>expand</div>
        </button>
        <button
            class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150"
            wire:click="toggleCustomPrompt">
            <x-icon name="speakerphone" width="18" height="18" />
            <div>Ask
                to...</div>
        </button>
    </div>

</div>
