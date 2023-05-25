<div class="flex flex-col gap-12">
    @livewire('blog.title', ['content' => $document->meta['title']])
    @livewire('blog.meta-description', ['content' => $document->meta['meta_description']])
    @livewire('blog.content-editor', [$document])

    @if($displayHistory)
        @livewire('blog.history-modal', [$document])
    @endif
</div>


@stack('scripts')
