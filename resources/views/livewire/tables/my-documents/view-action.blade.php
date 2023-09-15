<div class="flex items-center gap-2">
    <button type="submit"
        class="{{$canView ? 'bg-main hover:bg-secondary text-white' : 'bg-gray-300 text-gray-100' }} flex items-center rounded-full p-3"
        wire:click="viewDoc('{{$rowId}}')">
        <x-icon :name="$canView ? 'arrow-circle-right' : 'clock'" class="w-5 h-5" />
    </button>
    @if($canDelete) <button class="flex items-center bg-white hover:bg-zinc-300 border border-zinc-400 text-zinc-700 rounded-full p-3" wire:click="deleteDoc('{{$rowId}}')"><x-icon name="trash" class="w-5 h-5" /></button>@endif
</div>
