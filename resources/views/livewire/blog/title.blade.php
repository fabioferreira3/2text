<div class="flex flex-col gap-2 p-8 bg-zinc-300 rounded-xl border border-gray-200 border-primary border-opacity-10">
    <div class="flex justify-between">
        @include('livewire.common.label', ['title' => __('blog.title')])
        <div class="flex gap-2">
            @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true])
        </div>
    </div>
    <input placeholder="Post title" class="text-lg mt-2 p-3 rounded-lg border border-zinc-200" wire:model="content" type="text" name="title" />
</div>
