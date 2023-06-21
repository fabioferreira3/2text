<div class="flex items-center gap-2">
    <x-button.circle neutral lg :disabled="!$isCompleted" icon="arrow-circle-right" class="flex items-center bg-main hover:bg-secondary text-white" wire:click="viewDoc('{{$rowId}}')"/>
    <x-button.circle neutral lg :disabled="!$isCompleted" icon="trash" class="flex items-center bg-white hover:bg-zinc-300 border border-zinc-400 text-zinc-700" wire:click="deleteDoc('{{$rowId}}')"/>
</div>
