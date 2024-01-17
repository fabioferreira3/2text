<div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
        <x-icon name="hashtag" width="48" height="48" class="text-secondary" />
        <div class="flex flex-col">
            <h2 class="text-3xl text-primary font-bold">{{__('templates.start_here')}}</h1>
                <small class="text-zinc-700 text-lg">{{__('templates.choose_template')}}</small>
        </div>
    </div>
    <div class="grid md:grid-cols-3 xl:grid-cols-4 gap-6">
        @livewire('social-media-post.template')
        @livewire('blog.template')
        @livewire('image.template')
        @livewire('audio-transcription.template')
        @livewire('paraphraser.template')
        @livewire('text-to-audio.template')
        @livewire('summarizer.template')
        @livewire('insight-hub.template')
    </div>
</div>