<div class="flex flex-col gap-2 p-4 bg-zinc-200 rounded-lg border border-gray-300">
    <div class="flex justify-between">
        <label for="title" class="font-bold text-xl">Meta description</label>
        <div class="flex gap-2">
            @include('livewire.common.field-actions')
        </div>
    </div>
    <x-textarea class="mt-2 rounded-lg border border-zinc-200" name="meta_description" wire:model="content" rows="2"></x-textarea>
</div>