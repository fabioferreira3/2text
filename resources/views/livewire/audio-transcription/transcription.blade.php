<div class="flex flex-col gap-12">
    @include('livewire.common.header', ['icon' => 'video-camera', 'label' => $document->title])

    <div class="flex gap-6 p-6 rounded-lg border border-gray-300">
        <div class="flex flex-col gap-4 w-3/5">
            <div>
                <label for="content" class="font-bold text-xl">{{__('transcription.transcription')}}</label>
            </div>
            <div>
                @livewire('common.blocks.text-block', [$contentBlock], key($contentBlock->id))
            </div>
        </div>
        <div class="w-2/5">
            <div class="relative mt-10" style="padding-top: 100%;">
                <iframe class="absolute top-0 left-0 h-full w-full rounded-lg" src="https://www.youtube.com/embed/{{$document->getYoutubeVideoId()}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>