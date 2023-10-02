<div class="mb-8">
    @include('livewire.common.label', ['title' => $title])
</div>
<div class="flex justify-start mb-4">
    <button wire:click="copyPost" class="flex items-center gap-2 bg-secondary px-3 py-1 rounded-lg text-white">
        <x-icon name="clipboard-copy" width="18" height="18" />
        <div>Copy all</div>
    </button>
</div>
<div class="flex flex-col gap-2">
    @foreach ($document->contentBlocks as $contentBlock)
    @if($contentBlock->type === 'image') @else @livewire('common.blocks.text-block', [$contentBlock]) @endif
    @endforeach
</div>
