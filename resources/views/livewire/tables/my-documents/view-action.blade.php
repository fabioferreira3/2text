<div class="flex items-center gap-2">
    <x-button.circle :disabled="!$isCompleted" red icon="play" class="flex items-center gap-2" wire:click="viewDoc('{{$rowId}}')"/>
    <x-button.circle :disabled="!$isCompleted" gray icon="trash" class="flex items-center gap-2" wire:click="deleteDoc('{{$rowId}}')"/>
</div>
