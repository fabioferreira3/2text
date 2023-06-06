<div class="flex flex-col gap-2 p-4 bg-white rounded-lg border border-gray-200">
    <div class="flex justify-between">
        @include('livewire.common.label', ['title' => 'Meta description'])
        @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true])
    </div>
    <x-textarea class="mt-2 rounded-lg border border-zinc-200" name="meta_description" wire:model="content" rows="8"></x-textarea>
</div>
