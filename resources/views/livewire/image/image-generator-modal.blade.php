<x-experior::modal>
    <div class="py-4 text-left px-6">
        <div role='button' class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Describe the new image you want to generate:</p>
            <div role="button" class="cursor-pointer z-50" id="close" wire:click="toggle">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 18 18">
                    <path
                        d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                    </path>
                </svg>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4 mb-4">
            @if ($previewImgs->count())
                @foreach ($previewImgs as $img)
                    <div class="border border-gray-200 rounded-lg">
                        <img class="rounded-lg" src={{ $img->getUrl() }} />
                    </div>
                @endforeach
            @endif
        </div>
        <textarea
            placeholder="Example: Anime illustration of a character bonding with a majestic dragon in a secluded mountain sanctuary."
            class="w-full text-base border-0 bg-gray-100 p-0 rounded-xl py-3 px-4" rows="3" wire:model="prompt"></textarea>
        <div class="flex justify-start mt-8">
            <button wire:click="process" wire:loading.remove :disabled="$saving"
                class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-xl">
                @if (!$saving)
                    <x-icon name="play" class="w-8 h-8" />
                @endif
                @if ($saving)
                    <x-loader width="6" height="6" color="white" />
                @endif
                @if (!$saving)
                    <span>{{ __('social_media.go') }}</span>
                @endif
            </button>
        </div>
    </div>
</x-experior::modal>
