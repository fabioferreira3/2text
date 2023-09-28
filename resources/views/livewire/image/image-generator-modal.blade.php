<x-experior::modal>
    <div class="p-4">
        <div role='button' class="flex justify-between items-center pb-3">
            <div class="flex items-center gap-4 border-gray-200 border-b pb-2">
                <div class="w-2 h-12 md:h-6 bg-secondary"></div>
                <h1 class="text-2xl font-bold">
                    Generate new images or create variants of existing ones
                </h1>
            </div>
            <div role="button" class="cursor-pointer z-50" id="close" wire:click="toggleModal">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 18 18">
                    <path
                        d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                    </path>
                </svg>
            </div>
        </div>
        <div class="grid items-start grid-cols-2 gap-8 mt-4">
            @if ($previewImgs['original'])
            <div class="flex flex-col gap-2 h-full">
                <div class="text-2xl font-bold">Original:</div>
                <div class="relative group rounded-lg h-full flex items-center">
                    <img class="rounded-lg border border-gray-200" src={{ $previewImgs['original']['file_url'] }} />
                    <div
                        class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                        <div class="z-20 flex items-center justify-center gap-2">
                            <button @if ($processing) disabled @endif
                                wire:click="selectImage('{{ $previewImgs['original']['file_url'] }}')"
                                class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon solid name="thumb-up" class="w-6 h-6" />
                                {{-- <span>Use this one</span> --}}
                            </button>
                            <button @if ($processing) disabled @endif
                                wire:click="generateImageVariants('{{ $previewImgs['original']['id'] }}')"
                                class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon name="switch-horizontal" class="w-6 h-6" />
                                {{-- <span>Generate Variants</span> --}}
                            </button>
                            <button wire:click="downloadImage('{{ $previewImgs['original']['id'] }}')"
                                class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon name="download" class="w-6 h-6" />
                                {{-- <span>Download</span> --}}
                            </button>
                        </div>
                    </div>
                    <div
                        class="group-hover:opacity-60 rounded-lg absolute flex items-center justify-center inset-0 bg-black opacity-0 transition-opacity duration-300 ease-in-out">
                    </div>
                </div>
            </div>
            @endif

            @if (count($previewImgs['variants']))
            <div class="flex flex-col gap-2">
                <div class="text-2xl font-bold">Variants:</div>
                <div class="grid grid-cols-2 gap-4 mb-4 overflow-auto">
                    @foreach ($previewImgs['variants'] as $img)
                    <div class="relative group border border-gray-200 rounded-lg max-h-64">
                        <img class="rounded-lg object-cover h-[180px] w-full" src={{ $img['file_url'] }} />
                        <div
                            class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                            <div class="z-20 flex items-center gap-2">
                                <button @if ($processing) disabled @endif
                                    wire:click="selectImage('{{ $img['file_url'] }}')"
                                    class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon solid name="thumb-up" class="w-5 h-5" />
                                    {{-- <span>Use this one</span> --}}
                                </button>
                                <button @if ($processing) disabled @endif
                                    wire:click="generateImageVariants('{{ $img['id'] }}')"
                                    class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon name="switch-horizontal" class="w-5 h-5" />
                                    {{-- <span>Generate Variants</span> --}}
                                </button>
                                <button wire:click="downloadImage('{{ $img['id'] }}')"
                                    class="transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon name="download" class="w-5 h-5" />
                                    {{-- <span>Download</span> --}}
                                </button>
                            </div>
                        </div>
                        <div
                            class="group-hover:opacity-60 rounded-lg absolute flex items-center justify-center inset-0 bg-black opacity-0 transition-opacity duration-300 ease-in-out">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>


        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mt-4">
            <div class="flex flex-col gap-2 w-full">
                <div class="text-xl font-bold">Write the image description:</div>
                <textarea
                    placeholder="Example: Anime illustration of a character bonding with a majestic dragon in a secluded mountain sanctuary."
                    class="w-full text-base border-1 border-gray-200 bg-gray-100 p-0 rounded-xl p-4" rows="3"
                    wire:model="prompt"></textarea>

            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="text-xl font-bold">Select a style:</div>
                <x-dropdown class="w-full">
                    <x-slot name="trigger">
                        <button class="bg-gray-100 w-full p-4 text-gray-700 rounded-lg">
                            <div class="flex items-center gap-4">
                                @if ($selectedStylePreset)
                                <img class="w-20 h-20 rounded-lg" src={{ $selectedStylePreset['image_path'] }} />
                                @endif
                                <div>{{ $selectedStylePreset['label'] ?? 'Select' }}</div>
                            </div>
                        </button>
                    </x-slot>
                    @foreach ($this->stylePresets as $key => $stylePreset)
                    <x-dropdown.item wire:click="$set('imgStyle', '{{ $stylePreset['value'] }}')" :separator="$key > 0">
                        <div class="flex items-center gap-4 w-full">
                            <img class="w-20 h-20 rounded-lg" src={{ $stylePreset['image_path'] }} />
                            {{ $stylePreset['label'] }}
                        </div>
                    </x-dropdown.item>
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        <div class="flex justify-center mt-8">
            <button wire:click="generateNewImages" @if ($processing) disabled @endif
                class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-xl">
                @if (!$processing)
                <x-icon name="play" class="w-8 h-8" />
                @endif
                @if ($processing)
                <div class="flex items-center gap-4">
                    <x-loader width="6" height="6" color="white" />
                    <div>{{ __('social_media.generating') }}...</div>
                </div>
                @endif
                @if (!$processing)
                <span>
                    @if (!count($previewImgs['variants']))
                    {{ __('social_media.generate_new_image') }}
                    @endif
                    @if (count($previewImgs['variants']))
                    {{ __('social_media.regenerate') }}
                    @endif
                </span>
                @endif
            </button>
        </div>
    </div>
</x-experior::modal>