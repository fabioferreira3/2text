<x-experior::modal>
    <div class="p-4">
        <div class="flex justify-between items-center pb-3">
            <div class="flex items-center gap-4 border-gray-200 border-b pb-2">
                <div class="w-2 h-12 md:h-6 bg-secondary"></div>
                <h1 class="text-2xl font-bold">
                    {{__('modals.generate_new_images')}}
                </h1>
            </div>
            <div role="button" class="cursor-pointer z-50 bg-main p-1 rounded" id="close" wire:click="toggleModal">
                <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 18 18">
                    <path
                        d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                    </path>
                </svg>
            </div>
        </div>
        <div
            class="flex flex-col lg:grid @if($previewImgs['original'] && count($previewImgs['variants'])) 'lg:grid-cols-2' @endif items-start gap-8 mt-4">
            @if ($previewImgs['original'])
            <div class="flex flex-col gap-2 h-full w-full">
                <div class="text-2xl font-bold mb-8 md:mb-0">{{__('modals.primary')}}:</div>
                <div class="relative group rounded-lg h-full flex justify-center items-center">
                    <img class="rounded-lg object-cover border border-gray-200 h-96" src={{
                        $previewImgs['original']['file_url'] }} />
                    <div
                        class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                        <div class="z-20 flex items-center justify-center gap-2">
                            <button @if ($processing) disabled @endif
                                wire:click="selectImage('{{ $previewImgs['original']['id'] }}')"
                                class="relative group/button transition duration-200 text-white hover:bg-secondary border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon solid name="thumb-up" class="w-6 h-6" />
                                <div
                                    class="bg-black py-1 rounded absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                    {{__('images.use_primary')}}
                                </div>
                            </button>

                            {{-- <button @if ($processing) disabled @endif
                                wire:click="generateImageVariants('{{ $previewImgs['original']['id'] }}')"
                                class="relative group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon name="switch-horizontal" class="w-6 h-6" />
                                <div
                                    class="bg-black py-1 rounded absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                    {{__('images.generate_variants')}}
                                </div>
                            </button> --}}
                            <button wire:click="downloadImage('{{ $previewImgs['original']['id'] }}')"
                                class="relative group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon name="download" class="w-6 h-6" />
                                <div
                                    class="bg-black py-1 rounded absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                    {{__('images.download')}}
                                </div>
                            </button>
                            <button wire:click="previewImage('{{ $previewImgs['original']['id'] }}')"
                                class="relative group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-3 rounded-lg flex items-center gap-2">
                                <x-icon name="eye" class="w-6 h-6" />
                                <div
                                    class="bg-black py-1 rounded absolute top-10 mt-4 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                    {{__('images.preview')}}
                                </div>
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
            <div class="flex flex-col gap-2 w-full mt-8 md:mt-0">
                <div class="text-2xl font-bold">@if($action) {{$action . ":"}} @endif</div>
                <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
                    @foreach ($previewImgs['variants'] as $key => $mediaFile)
                    <div wire:key="{{$key}}" class="relative group border border-gray-200 rounded-lg max-h-64">
                        <img class="rounded-lg object-cover w-full lg:h-44 xl:h-64" src={{ $mediaFile['file_url'] }} />
                        <div
                            class="absolute top-4 left-4 text-2xl font-bold text-black bg-white opacity-50 rounded-lg px-2">
                            #{{$key+1}}</div>
                        <div
                            class="hidden group-hover:flex absolute top-0 left-0 h-full w-full items-center justify-center">
                            <div class="relative z-20 grid grid-cols-2 xl:flex xl:flex-row text-lg items-center gap-2">
                                <button @if ($processing) disabled @endif
                                    wire:click="selectImage('{{ $mediaFile['id'] }}')"
                                    class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon solid name="thumb-up" class="w-5 h-5" />
                                    <div
                                        class="bg-black py-1 rounded absolute -bottom-10 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                        {{__('images.use_primary')}}
                                    </div>
                                </button>
                                {{-- <button @if ($processing) disabled @endif
                                    wire:click="generateImageVariants('{{ $mediaFile['id'] }}')"
                                    class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon name="switch-horizontal" class="w-5 h-5" />
                                    <div
                                        class="bg-black py-1 rounded absolute -bottom-10 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                        {{__('images.generate_variants')}}
                                    </div>
                                </button> --}}
                                <button wire:click="downloadImage('{{ $mediaFile['id'] }}')"
                                    class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon name="download" class="w-5 h-5" />
                                    <div
                                        class="bg-black py-1 rounded absolute -bottom-10 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                        {{__('images.download')}}
                                    </div>
                                </button>
                                <button wire:click="previewImage('{{ $mediaFile['id'] }}')"
                                    class="group/button transition-bg delay-100 duration-200 text-white hover:bg-secondary hover:border-transparent border border-gray-400 bg-gray-500 p-2 rounded-lg flex items-center gap-2">
                                    <x-icon name="eye" class="w-5 h-5" />
                                    <div
                                        class="bg-black py-1 rounded absolute -bottom-10 w-[150px] left-1/2 transform -translate-x-1/2 mt-2 opacity-0 group-hover/button:opacity-100 transition-opacity duration-200 ease-in-out tooltip">
                                        {{__('images.preview')}}
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

        <div class="flex flex-col gap-6 mt-4">
            <div class="flex flex-col gap-2 w-full">
                <div class="text-xl font-bold">{{ __('images.write_image_description_changes') }}:</div>
                <textarea placeholder="{{__('images.placeholder_example')}}"
                    class="w-full text-base border-1 border-gray-200 bg-gray-100 p-0 rounded-xl p-4" rows="3"
                    wire:model.live="prompt"></textarea>

            </div>
            <div class="flex justify-center items-center gap-2">
                <div class="text-xl font-bold">{{ __('images.samples') }}:</div>
                <input type="number" wire:model.live="samples" class="border border-gray-200 rounded-lg" min="1"
                    max="8" />
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
                    <div>{{ __('social_media.generating_images')}}...</div>
                </div>
                @endif
                @if (!$processing)
                <span>
                    {{ __('social_media.generate_new_image') }}
                </span>
                @endif
            </button>
        </div>
    </div>
</x-experior::modal>
