<div class="flex flex-col gap-2">
    @foreach ($document->contentBlocks as $contentBlock)
    @if($contentBlock->type === 'image') @else @livewire('common.blocks.text-block', [$contentBlock]) @endif
    @endforeach
</div>
