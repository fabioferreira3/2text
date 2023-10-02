<div>
    <div class="mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex items-center gap-2 justify-start mb-4">
        <button wire:click="$toggle('showInfo')"
            class="flex items-center gap-2 bg-gray-500 px-3 py-1 rounded-lg text-gray-100">
            <x-icon name="information-circle" width="18" height="18" />
            <div>Info</div>
        </button>
        <button type="button" wire:click="copyPost"
            class="flex items-center gap-2 bg-secondary px-3 py-1 rounded-lg text-white">
            <x-icon name="clipboard-copy" width="18" height="18" />
            <div>Copy all</div>
        </button>
    </div>
    <div class="flex flex-col gap-2">
        @foreach ($document->contentBlocks as $contentBlock)
        @if($contentBlock->type === 'image_media_file')
        @livewire('common.blocks.img-block', [
        $contentBlock
        ], key($contentBlock->id)) @else
        @livewire('common.blocks.text-block', [
        $contentBlock
        ], key($contentBlock->id))
        @endif
        @endforeach
    </div>
    @if ($showInfo)
    <x-experior::modal>
        <div class="p-2">
            <div class="flex items-center justify-between">
                @include('livewire.common.label', ['title' => $title])
                <div role="button" class="cursor-pointer z-50" id="close" wire:click="$toggle('showInfo')">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 18 18">
                        <path
                            d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="flex flex-col gap-2 mt-8">
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Keyword:</span>
                    <span class="col-span-2">123{{$document->keyword }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Source:</span>
                    <span class="col-span-2">{{$document->source }}</span>
                </div>
                @if ($document->getMeta('context'))
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Context:</span>
                    <span class="col-span-2 max-h-48 overflow-auto">{{$document->getMeta('context')}}</span>
                </div>
                @endif
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Tone:</span>
                    <span>{{$document->getMeta('tone') }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Style:</span>
                    <span class="col-span-2">{{$document->getMeta('style') }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="font-bold col-span-1">Word count:</span>
                    <span>{{$document->word_count }}</span>
                </div>
            </div>
        </div>
    </x-experior::modal>
    @endif
</div>
