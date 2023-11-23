<div class="flex flex-col gap-4">
    <div class="flex flex-col">
        <div>
            <h2 class="text-3xl text-primary font-bold">{{__('templates.start_here')}}</h1>
                <small class="text-zinc-700 text-lg">{{__('templates.choose_template')}}</small>
        </div>
    </div>
    <div class="grid md:grid-cols-3 xl:grid-cols-4 gap-6">
        @livewire('social-media-post.template')
        @livewire('blog.template')
        @livewire('audio-transcription.template')
        @livewire('paraphraser.template')
        @livewire('text-to-speech.template')
        @livewire('summarizer.template')
        @livewire('inquiry-hub.template')
    </div>
</div>