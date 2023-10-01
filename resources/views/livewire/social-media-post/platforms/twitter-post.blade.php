<div class="flex flex-col">
    <div class='flex items-center justify-between bg-white rounded-t-xl border border-zinc-200 px-6 py-4'>
        <img class="h-12" src="{{ Vite::asset('resources/images/twitter-logo.png') }}">
        @include('livewire.social-media-post.post-actions')
    </div>
    <div class="border-l border-r border-b border-zinc-200 rounded-b-xl overflow-hidden flex-grow">
        <div class="flex flex-col h-full px-6 pt-6 pb-2 bg-black rounded-b-xl">
            <div class="flex-1">
                @include('livewire.social-media-post.social-media-image', ['image' => $image])
                @livewire('social-media-post.text-block', ['content' => $text, 'contentBlockId' => $textBlockId,
                'faster' =>
                true])
            </div>
            <div class="flex items-center gap-2 justify-end mt-2">
                @if (!$saving)
                <x-icon name="badge-check" width="30" height="30" class="text-white" />
                @endif
                @if ($saving)
                <x-loader color="white" weight="6" height="6" />
                @endif
            </div>
        </div>
    </div>
    @if ($showImageGenerator)
    @livewire('image.image-generator-modal', ['contentBlock' => $imageBlock])
    @endif
</div>
