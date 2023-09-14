<div class="flex items-center gap-2">
    @if($canView)<button type="submit" class="flex items-center bg-main hover:bg-secondary text-white rounded-full p-3" wire:click="viewDoc('{{$rowId}}')"><x-icon name="arrow-circle-right" class="w-5 h-5" /></button>@endif
    @if(!$canView)<button class="flex items-center bg-gray-300 text-gray-100 rounded-full p-3"><x-icon name="clock" class="w-5 h-5" /></button>@endif
    @if($canDelete) <button class="flex items-center bg-white hover:bg-zinc-300 border border-zinc-400 text-zinc-700 rounded-full p-3" wire:click="deleteDoc('{{$rowId}}')"><x-icon name="trash" class="w-5 h-5" /></button>@endif
</div>
