<div class="flex flex-col gap-12">
    @include('livewire.common.header', ['icon' => 'video-camera', 'label' => $document->title])

    <div class="flex flex-col md:flex-row gap-6 p-6 rounded-lg border border-gray-300">
        <div class="flex flex-col order-2 md:order-none gap-4 w-full md:w-3/5">
            <div>
                <label for="content" class="font-bold text-xl">{{__('transcription.transcription')}}</label>
            </div>
            <div class="flex flex-col gap-2">
                @foreach ($contentBlocks as $contentBlock)
                @livewire('common.blocks.text-block', [$contentBlock], key($contentBlock->id))
                @endforeach
            </div>
        </div>
        <div class="w-full order-1 md:order-none md:w-2/5">
            <div>download</div>
            <div class="sticky top-0 z-10">
                <div class="relative md:mt-10" style="padding-top: 100%;">
                    <iframe class="absolute top-0 left-0 h-full w-full rounded-lg"
                        src="https://www.youtube.com/embed/{{$document->getYoutubeVideoId()}}"
                        title="YouTube video player"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
