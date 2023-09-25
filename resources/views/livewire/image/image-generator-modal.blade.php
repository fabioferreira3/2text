<x-experior::modal>
    <div class="py-4 text-left px-6">
        <div role='button' class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">
                @if (!$previewImgs->count())
                    Describe the new image you want to generate:
                @endif
                @if ($previewImgs->count())
                    Choose the image you want to use:
                @endif
            </p>
            <div role="button" class="cursor-pointer z-50" id="close" wire:click="toggle">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 18 18">
                    <path
                        d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                    </path>
                </svg>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            @if ($previewImgs->count())
                @foreach ($previewImgs as $key => $img)
                    <div class="relative group border border-gray-200 rounded-lg">
                        <img class="rounded-lg" src={{ $img->getUrl() }} />
                        <div
                            class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                            <div class="z-20 flex flex-col gap-2">
                                <button disabled="{{ $saving }}" wire:click="selectImg({{ $key }})"
                                    class="text-zinc-200 bg-zinc-800 hover:bg-zinc-600 border border-zinc-700 px-2 py-1 rounded-lg flex items-center gap-2">
                                    <x-icon solid name="thumb-up" class="w-5 h-5" />
                                    <span>Use this one</span>
                                </button>
                                <button disabled="{{ $saving }}" wire:click="processVariants({{ $key }})"
                                    class="text-white hover:bg-[#e04595] bg-secondary px-2 py-1 rounded-lg flex items-center gap-2">
                                    <x-icon name="switch-horizontal" class="w-5 h-5" />
                                    <span>Generate Variants</span>
                                </button>
                                <button wire:click="downloadImage({{ $key }})"
                                    class="border border-white border-zinc-600 text-white bg-main hover:bg-[#1c1f5b] font-medium px-3 py-1 rounded-lg flex items-center gap-2">
                                    <x-icon name="download" class="w-5 h-5" />
                                    <span>Download</span>
                                </button>
                            </div>
                        </div>
                        <div
                            class="group-hover:opacity-60 rounded-lg absolute flex items-center justify-center inset-0 bg-black opacity-0 transition-opacity duration-300 ease-in-out">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <textarea
            placeholder="Example: Anime illustration of a character bonding with a majestic dragon in a secluded mountain sanctuary."
            class="w-full text-base border-0 bg-gray-100 p-0 rounded-xl py-3 px-4" rows="3" wire:model="prompt"></textarea>
        <div class="flex justify-start mt-8">
            <button wire:click="processNew" wire:loading.remove :disabled='$processing'
                class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-xl">
                @if (!$processing)
                    <x-icon name="play" class="w-8 h-8" />
                @endif
                @if ($processing)
                    <x-loader width="6" height="6" color="white" />
                @endif
                @if (!$processing)
                    <span>
                        @if (!$previewImgs->count())
                            {{ __('social_media.generate') }}
                        @endif
                        @if ($previewImgs->count())
                            {{ __('social_media.regenerate') }}
                        @endif
                    </span>
                @endif
            </button>
        </div>
    </div>
</x-experior::modal>
