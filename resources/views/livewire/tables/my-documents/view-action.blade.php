<div class="flex items-center gap-2">
    <button class="flex items-center gap-2 bg-zinc-200 text-zinc-900 px-3 py-2 rounded-lg" wire:click="viewDoc('{{$rowId}}')">
        <i class="fa-regular fa-eye"></i></button>
        <button class="flex items-center gap-2 bg-zinc-800 text-zinc-200 px-3 py-2 rounded-lg" wire:click="deleteDoc('{{$rowId}}')">
            <i class="fa-solid fa-trash"></i></button>
</div>
