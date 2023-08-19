<div class="flex items-center gap-2">
    @if($canView)<x-button.circle neutral lg icon="external-link" class="flex items-center bg-main hover:bg-secondary text-white" wire:click="viewDoc('{{$rowId}}')"/>@endif
    @if(!$canView)<x-button.circle neutral lg icon="beaker" class="flex items-center bg-gray-300"/>@endif
    @if($canDelete) <x-button.circle neutral lg icon="trash" class="flex items-center bg-white hover:bg-zinc-300 border border-zinc-400 text-zinc-700" wire:click="deleteDoc('{{$rowId}}')"/>@endif
</div>
