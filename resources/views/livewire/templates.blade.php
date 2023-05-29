<div>
    <div class="flex flex-col gap-2 mb-8">
        <h1 class="text-4xl font-bold mb-6">Templates</h1>
        <h2 class="text-3xl font-bold">Start here</h1>
        <small class="text-lg font-thin">Choose a template and let's write some content!</small>
    </div>
    <div class="grid md:grid-cols-3 xl:grid-cols-4 gap-6">
        @livewire('blog.template')
        @livewire('text-transcription.template')
    </div>
</div>
