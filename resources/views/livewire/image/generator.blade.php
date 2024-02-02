<div class="p-4 h-1/2">
    <div role='button' class="flex justify-between items-center pb-3">
        <div class="flex items-center gap-4 border-gray-200 border-b pb-2">
            <div class="w-2 h-12 md:h-6 bg-secondary"></div>
            <h1 class="text-2xl font-bold">
                {{__('images.generate_image')}}
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
    <div class="flex flex-col lg:grid-cols-2 items-start gap-8 mt-4">
        @if($processing)
        <div class="w-full">
            <div class="text-2xl font-bold mb-8">{{__('images.images')}}</div>
            <div class="grid grid-cols-2 gap-4">
                @for($i = 1; $i <= $samples; $i++) <div
                    class="bg-black opacity-60 rounded-lg h-48 w-full flex items-center justify-center">
                    <x-loader width="16" height="16" color="white" />
            </div>
            @endfor
        </div>
    </div>
    @endif

    @if (count($previewImgs) && !$processing)
    <div class="flex flex-col gap-2 w-full mt-8 md:mt-0">
        <div class="text-2xl font-bold">{{__('images.images')}}</div>
        <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
            @foreach ($previewImgs as $key => $mediaFile)
            <div class="relative group border border-gray-200 rounded-lg max-h-96">
                <img class="rounded-lg object-cover w-full lg:h-44 xl:h-96" src={{ $mediaFile['file_url'] }} />
                <div class="absolute top-4 left-4 text-2xl font-bold text-black bg-white opacity-50 rounded-lg px-2">
                    #{{$key+1}}</div>
                <div class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                    <div class="relative z-20 grid grid-cols-2 xl:flex xl:flex-row text-lg items-center gap-2">
                        {{-- <button wire:click="generateVariants('{{ $mediaFile['id'] }}')"
                            class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                            <x-icon name="switch-horizontal" class="w-5 h-5" />
                            <div
                                class="absolute -bottom-9 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.generate_variants') }}
                            </div>
                        </button> --}}
                        <button wire:click="downloadImage('{{ $mediaFile['id'] }}')"
                            class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                            <x-icon name="download" class="w-5 h-5" />
                            <div
                                class="absolute -bottom-9 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.download') }}
                            </div>
                        </button>
                        <button wire:click="previewImage('{{ $mediaFile['id'] }}')"
                            class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                            <x-icon name="eye" class="w-5 h-5" />
                            <div
                                class="absolute -bottom-9 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.preview') }}
                            </div>
                        </button>
                    </div>
                </div>
                @if ($processing)
                <div class="opacity-60 rounded-lg absolute flex items-center justify-center inset-0 bg-black">
                    <x-loader height="12" width="12" color="white" />
                </div>
                @endif
                <div
                    class="group-hover:opacity-60 rounded-lg absolute flex items-center justify-center inset-0 bg-black opacity-0 transition-opacity duration-300 ease-in-out">
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($errorGenerating)
<div class="w-1/2">
    <div class="flex items-center justify-center border border-gray-200 rounded-lg h-48">
        It was not possible to generate this image.
        Please use a different prompt.
    </div>
</div>

@endif

<div class="flex flex-col md:items-center md:justify-between gap-6 mt-4">
    <div class="flex flex-col gap-2 w-full">
        <div class="flex flex-col gap-2 md:flex-row items-center justify-between">
            <div class="text-xl font-bold">{{ __('images.describe_new_image') }}:</div>

            <!-- Image guidelines -->
            <div class="relative w-full md:w-auto place-self-end" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = true"
                    class="h-8 2-8 w-full justify-center flex gap-2 items-center bg-gray-500 text-white px-3 rounded-lg">
                    <x-icon name="exclamation" class="h-5 w-5" />
                    <span class="text-sm">{{ __('instructions.check_img_guidelines')}}</span>
                </button>

                <div class="fixed overflow-auto max-h-96 flex flex-col gap-4 bg-white border shadow-lg p-4 w-auto md:w-80 rounded-lg z-[100]"
                    x-show="open" @mouseover="open = true" style="display: none;">
                    <div class="flex gap-2 items-center justify-between md:justify-end w-full">
                        <div class="font-bold text-lg">{{ __('instructions.rejected_requests') }}:</div>
                        <x-button icon="x" sm @click="open = false" />
                    </div>
                    <div>
                        {!! App\Helpers\InstructionsHelper::imageGuidelines() !!}
                    </div>
                </div>
            </div>
            <!-- END: Image guidelines -->
        </div>
        <textarea placeholder="{{__('images.example')}}"
            class="w-full text-base border-1 border-gray-200 bg-gray-100 p-0 rounded-xl p-4" rows="3"
            wire:model="prompt"></textarea>
    </div>
    <div class="flex justify-center items-center gap-2">
        <div class="text-xl font-bold">{{ __('images.samples') }}:</div>
        <input type="number" wire:model="samples" class="border border-gray-200 rounded-lg" min="1" max="8" />
    </div>
    <div class="bg-gray-200 px-3 py-1 rounded-lg text-sm font-semibold text-gray-700">
        1 {{ strtolower(__('common.image'))}} = 1 {{__('common.unit')}}
    </div>
    <!-- <div class="flex flex-col gap-2 w-full">
            <div class="text-xl font-bold">{{ __('social_media.select_style') }}:</div>
            <x-custom.dropdown class="w-full z-100" direction="down">
                <x-slot name="trigger">
                    <button class="bg-gray-100 w-full p-4 text-gray-700 rounded-lg">
                        <div class="flex items-center gap-4">
                            {{-- @if ($selectedStylePreset)
                            <img class="w-20 h-20 rounded-lg" src={{ $selectedStylePreset['image_path'] }} />
                            @endif --}}
                            {{-- <div>{{ $selectedStylePreset['label'] ?? 'Select' }}</div> --}}
                        </div>
                    </button>
                </x-slot>
                {{-- @foreach ($this->stylePresets as $key => $stylePreset)
                <x-dropdown.item class="hover:bg-gray-100" wire:click="$set('imgStyle', '{{ $stylePreset['value'] }}')"
                    :separator="$key > 0">
                    <div class="flex items-center gap-4 w-full">
                        <img class="w-20 h-20 rounded-lg" src={{ $stylePreset['image_path'] }} />
                        <div class="text-lg">{{ $stylePreset['label'] }}</div>
                    </div>
                </x-dropdown.item>
                @endforeach --}}
            </x-custom.dropdown>
        </div> -->
</div>

<div class="flex justify-center mt-8">
    <button wire:click="generate" @if ($processing) disabled @endif
        class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-xl">
        @if (!$processing)
        <x-icon name="play" class="w-8 h-8" />
        @endif
        @if ($processing)
        <div class="flex items-center gap-4">
            <x-loader width="6" height="6" color="white" />
            <div>{{ __('images.generating') }}...</div>
        </div>
        @endif
        @if (!$processing)
        <span>
            {{ __('images.generate') }}
        </span>
        @endif
    </button>
</div>

@if ($shouldPreviewImage && $selectedImage)
<x-experior::modal>
    <div class="flex items-center justify-end mb-4">
        <div role="button" class="cursor-pointer z-50" id="close" wire:click="$toggle('shouldPreviewImage')">
            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                viewBox="0 0 18 18">
                <path
                    d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                </path>
            </svg>
        </div>
    </div>
    <img src="{{$selectedImage->file_url}}" class="h-full w-full" />
</x-experior::modal>
@endif
</div>