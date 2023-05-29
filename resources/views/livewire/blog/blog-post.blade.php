<div class="flex flex-col gap-12">
    @livewire('blog.title', [$document])
    @livewire('blog.meta-description', [$document])
    @livewire('blog.content-editor', [$document])

    @if($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif
</div>


@stack('scripts')