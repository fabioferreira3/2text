<div class="flex flex-col gap-12">
    @include('livewire.common.header', ['icon' => 'video-camera', 'label' => $document->title])

    <div class="flex flex-col lg:flex-row gap-6 p-6 rounded-lg border border-gray-300">
        <div class="flex flex-col order-2 lg:order-none gap-4 w-full lg:w-1/2 xl:2/3">
            <div>
                <label for="content" class="font-bold text-xl">{{__('transcription.transcription')}}</label>
            </div>
            <div class="flex flex-col gap-2">
                @foreach ($contentBlocks as $contentBlock)
                @livewire('common.blocks.text-block', [$contentBlock], key($contentBlock->id))
                @endforeach
            </div>
        </div>
        <div class="flex flex-col order-1 lg:order-none w-full lg:w-1/2 xl:1/3">
            @if($document->getMeta('vtt_file_path') || $document->getMeta('srt_file_path'))
            <div class="group place-self-end flex flex-col">
                <div
                    class="cursor-pointer bg-gray-300 font-bold text-gray-700 px-3 py-2 group-hover:rounded-b-none rounded-lg">
                    {{__('transcription.download_subtitles')}}
                </div>

                <div class="hidden group-hover:flex flex-col bg-gray-100 rounded-b-lg">
                    @if($document->getMeta('vtt_file_path') ?? false)
                    <button type="button" wire:click="downloadSubtitle('vtt')"
                        class="text-gray-700 hover:bg-gray-200 px-3 py-2 text-end font-bold">
                        {{__('transcription.vtt_file')}}</button>
                    @endif
                    @if($document->getMeta('srt_file_path') ?? false)
                    <button type="button" wire:click="downloadSubtitle('srt')"
                        class="text-gray-700 hover:bg-gray-200 px-3 py-2 text-end font-bold rounded-b-lg">
                        {{__('transcription.srt_file')}}</button>
                    @endif
                </div>
            </div>
            @endif

            <div class="sticky top-0 z-10">
                <div class="relative mt-4 xl:mt-10" style="padding-top: 100%;">
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