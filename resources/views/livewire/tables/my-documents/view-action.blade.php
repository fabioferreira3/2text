<div class="flex items-center gap-2">
    @if ($canView)
    <button type="button"
        class="bg-secondary border border-secondary hover:border-main hover:bg-main text-white transition ease-in duration-300 flex items-center rounded-full p-3"
        wire:click="viewDoc('{{ $rowId }}')">
        <x-icon name="chevron-right" class="w-6 h-6" />
    </button>
    @else
    <button disabled type="button" class="bg-gray-400 text-gray-100 flex items-center rounded-full p-3">
        <x-icon name="clock" class="w-6 h-6" />
    </button>
    @endif
    @if ($canDelete)
    <button
        class="flex items-center bg-white hover:bg-zinc-300 transition ease-in duration-300 border border-zinc-500 text-zinc-700 rounded-full p-3"
        wire:click="deleteDoc('{{ $rowId }}')">
        <x-icon name="trash" class="w-6 h-6" />
    </button>
    @endif
</div>
