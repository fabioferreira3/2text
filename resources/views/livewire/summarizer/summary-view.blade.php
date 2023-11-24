<div class="flex flex-col gap-6">
    @section('header')
    @include('livewire.common.header', ['icon' => 'sort-ascending', 'title' => $title, 'suffix' => $context ?
    __('summarizer.summary') : ""])
    @endsection

    <div class="flex items-center justify-end gap-2">
        <button wire:click="new()" class="flex items-center gap-1 bg-main text-white font-bold px-5 py-1 rounded-lg">
            <x-icon name="plus" width="24" height="24" />
            <span class="text-lg">{{__('summarizer.new')}}</span>
        </button>
    </div>

    <div class="grid @if($context) grid-cols-2 @else grid-cols-1 @endif gap-8">
        <div class="flex flex-col gap-4">
            @if ($context)
            <h2 class="font-bold text-3xl text-zinc-700">{{ __('summarizer.source') }}:</h2>
            <div class="text-zinc-700">
                @if($source === 'youtube')
                <div class="relative" style="padding-top: 100%;">
                    <iframe class="absolute top-0 left-0 h-full w-full rounded-lg"
                        src="https://www.youtube.com/embed/{{$document->getYoutubeVideoId()}}"
                        title="YouTube video player"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>
                @else
                <div class="text-xl">
                    {{ $context }}
                </div>
                @endif
            </div>
            @endif
        </div>
        <div class="flex flex-col gap-4">
            <h2 class="font-bold text-3xl text-zinc-700">{{ __('summarizer.summary') }}:</h2>
            <div>
                @livewire('common.blocks.text-block', [
                $contentBlock,
                'hide' => ['delete']
                ], key($contentBlock->id))
            </div>
        </div>
    </div>
</div>
