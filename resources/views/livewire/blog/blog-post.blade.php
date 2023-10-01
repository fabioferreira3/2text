<div class="flex flex-col">
    {{-- @livewire('blog.title', [$document]) --}}
    {{-- @livewire('blog.meta-description', [$document]) --}}

    {{-- @if($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif --}}

    @foreach ($document->contentBlocks as $contentBlock)
    @livewire('common.blocks.text-block', [$contentBlock])
    @endforeach
</div>


@stack('scripts')
