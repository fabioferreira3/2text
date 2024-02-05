<div class="flex flex-col gap-2 p-4 bg-zinc-300 rounded-xl border border-gray-200 border-primary border-opacity-10">
    <div class="flex justify-between">
        @include('livewire.common.label', ['title' => __('blog.meta_description')])
        @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true])
    </div>
    <textarea class="mt-2 text-lg rounded-lg border border-zinc-200" name="meta_description" wire:model.live="content" rows="6"></textarea>
</div>
