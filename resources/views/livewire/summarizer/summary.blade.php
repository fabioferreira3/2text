<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'sort-ascending', 'label' => __('summarizer.summary')])
    <div>
        @livewire('common.blocks.text-block', [
        $contentBlock,
        'hide' => ['delete']
        ], key($contentBlock->id))
    </div>
</div>