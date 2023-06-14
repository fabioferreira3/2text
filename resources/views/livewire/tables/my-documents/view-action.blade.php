<div class="flex items-center gap-2">
    <x-button.circle lg :disabled="!$isCompleted" icon="arrow-circle-right" class="flex items-center bg-main text-white" wire:click="viewDoc('{{$rowId}}')"/>
    <x-button.circle lg :disabled="!$isCompleted" icon="trash" class="flex items-center bg-white text-zinc-700" wire:click="deleteDoc('{{$rowId}}')"/>
</div>
