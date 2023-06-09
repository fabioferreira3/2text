<div class="flex items-center gap-2">
    <x-button.circle lg :disabled="!$isCompleted" white icon="arrow-circle-right" class="flex items-center gap-2" wire:click="viewDoc('{{$rowId}}')"/>
    <x-button.circle sm :disabled="!$isCompleted" gray icon="trash" class="flex items-center gap-2 bg-primary" wire:click="deleteDoc('{{$rowId}}')"/>
</div>
