<div class="flex flex-col gap-2 p-4 bg-zinc-200 rounded-lg border border-gray-300">
    <div class="flex justify-between">
        <div class="flex gap-2">
            @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true])
        </div>
    </div>
    <x-textarea class="mt-2 rounded-lg border border-zinc-200" name="content" wire:model="content" rows="{{$rows}}"></x-textarea>
</div>
