<div class="p-4">
    <div class="flex items-center justify-center mb-4 mt-2">
        <button wire:click="$toggle('showNewGenerator')"
            class="flex items-center gap-2 bg-secondary px-4 py-2 rounded-lg text-white font-bold text-lg">
            <x-icon name="plus-sm" width="28" height="28" color="white" />
            <div>{{__('images.new')}}</div>
        </button>
    </div>

    @if(count($images))
    <div class="flex items-center justify-between my-4">
        <div class="text-2xl font-bold text-gray-700">Results: {{count($images)}}</div>
    </div>
    @endif

    @if(count($images))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($images as $image)
        <div class="h-[300px] relative group">
            <img wire:click="selectImage('{{$image->id}}')" src={{$image->file_url}} class="rounded-lg w-full
            h-full object-cover" loading="lazy"/>
            <div
                class="hidden group-hover:flex absolute top-0 left-0 h-full w-full flex-col items-center justify-center gap-12">
                <div class="flex items-center justify-center">
                    <div class="z-20 flex gap-2">
                        {{-- <button wire:click="generateVariants('{{$image->id}}')"
                            class="relative group/button transition duration-200 text-white hover:bg-secondary border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                            <x-icon solid name="photograph" class="w-5 h-5" />
                            <div
                                class="absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.generate_variants') }}
                            </div>
                        </button> --}}
                        <button wire:click="downloadImage('{{$image->id}}')"
                            class="relative group/button transition duration-200 text-white hover:bg-secondary border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                            <x-icon solid name="arrow-circle-down" class="w-5 h-5" />
                            <div
                                class="absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.download') }}
                            </div>
                        </button>
                        <button wire:click="previewImage('{{$image->id}}')"
                            class="relative group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                            <x-icon name="eye" class="w-5 h-5" />
                            <div
                                class="absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.preview') }}
                            </div>
                        </button>
                        <button wire:click="deleteImage('{{$image->id}}')"
                            class="relative group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                            <x-icon name="trash" class="w-5 h-5" />
                            <div
                                class="absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                {{ __('images.delete') }}
                            </div>
                        </button>
                    </div>
                </div>
                @if (isset($image->meta['prompt']))
                <div class="z-50 max-h-28 overflow-auto px-6">
                    <div class="text-white">"{{$image->meta['prompt']}}"</div>
                </div>
                @endif
            </div>
            <div
                class="group-hover:opacity-60 absolute flex items-center justify-center inset-0 bg-black rounded-t-xl opacity-0 transition-opacity duration-300 ease-in-out">
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if ($shouldPreviewImage)
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
    {{-- @if ($showVariantsGenerator)
    <x-experior::modal>
        @livewire('image.variants-generator', ['main' => $selectedImage])
    </x-experior::modal>
    @endif --}}

    @if ($showNewGenerator)
    <x-experior::modal>
        @livewire('image.image-generator')
    </x-experior::modal>
    @endif
</div>