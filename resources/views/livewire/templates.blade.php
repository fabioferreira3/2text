<div>
    <div class="flex flex-col gmb-8">
        @include('livewire.common.header', ['icon' => 'puzzle', 'label' => 'Templates'])
        <div class="mt-12">
            <h2 class="text-3xl text-zinc-700 font-bold">Start here</h1>
            <small class="text-zinc-700 text-lg">Choose a template and let's write some content!</small>
        </div>
    </div>
    <div class="grid md:grid-cols-3 xl:grid-cols-4 gap-6 mt-12">
        @livewire('social-media-post.template')
        @livewire('blog.template')
        @livewire('text-transcription.template')
    </div>
</div>
