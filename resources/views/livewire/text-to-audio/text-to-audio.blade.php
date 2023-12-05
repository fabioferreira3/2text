<div class="flex flex-col gap-6 h-full md:overflow-hidden">
    @section('header')
    <div class="flex items-center justify-between">
        @include('livewire.common.header', ['icon' => 'volume-up', 'title' => __('text-to-audio.text_to_audio')])
        <a href={{route('text-to-audio-history')}} class="transition-colors ease-in-out duration-500 delay-150
            flex items-center justify-center gap-2 bg-zinc-300 border border-zinc-100 hover:bg-zinc-400 hover:text-white
            py-2 px-3 rounded-lg text-sm text-gray-700">
            <x-icon class="w-5 h-5" name="book-open" />
            <div class="font-bold text-base">{{__('text-to-audio.history')}}
            </div>
        </a>
    </div>
    @endsection

    <div class="flex flex-col bg-white rounded-lg flex-grow md:overflow-hidden">
        <!-- Tab Selector -->
        <div class="flex items-center text-zinc-700">
            <div wire:click="$set('selectedTab', 'new')" class="@if($selectedTab !== 'new') cursor-pointer
                    text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif flex items-center gap-2
                    border-t border-l border-zinc-200 border-b-0 hover:bg-zinc-100 rounded-tl-lg px-6 py-2">
                <x-icon name="document-text" class="text-secondary" width="24" height="24" />
                <h2 class="text-lg">
                    {{__('dashboard.my_documents')}}</h2>
            </div>
            <div wire:click="$set('selectedTab', 'history')" class="@if($selectedTab !== 'history') cursor-pointer
                    text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif
                    flex items-center gap-2 border border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 px-6 py-2">
                <x-icon name="photograph" class="text-secondary" width="24" height="24" />
                <h2 class="text-lg">{{__('dashboard.my_images')}}</h2>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-zinc-100 rounded-b-lg rounded-r-lg px-4 pb-4 pt-4 border border-zinc-200 h-full">
            @if ($selectedTab === 'new')
            <div>
                @if ($currentAudioFile && !$isProcessing)
                <div class="w-full md:w-1/4 flex flex-col items-center gap-4 md:flex-row">
                    <button wire:click="processAudio('listen_current_audio')"
                        class="transition-colors ease-in-out duration-500
                            delay-150 flex items-center justify-center gap-2 bg-secondary border border-secondary hover:bg-zinc-200
                            hover:text-zinc-700 hover:border hover:border-zinc-300 py-2 px-3 rounded-lg text-sm text-white w-full">
                        <x-icon class="w-5 h-5" name="volume-up" />
                        <div class="font-bold text-base">{{$isPlaying ? __('text-to-audio.stop') :
                            __('text-to-audio.listen')}}
                        </div>
                    </button>
                    <audio id="listen_current_audio" src="{{ $currentAudioUrl }}" preload="auto" wire:ignore></audio>
                    <button class="transition-colors ease-in-out duration-500 delay-150 flex items-center justify-center gap-2
                            bg-main border border-main hover:bg-zinc-200 hover:text-zinc-700 hover:border hover:border-zinc-300 py-2 px-3
                            rounded-lg text-sm text-white w-full" wire:click='downloadAudio'>
                        <x-icon class="w-5 h-5" name="cloud-download" />
                        <div class="font-bold text-base">{{__('text-to-audio.download')}}</div>
                    </button>
                </div>
                @endif

                <div class="flex flex-col md:flex-row gap-4 overflow-auto h-full md:h-[40rem]">
                    <!-- Voice selector -->
                    <div class="flex flex-col w-full md:w-1/2 gap-4 py-4 h-[20rem] md:h-full">
                        <div class="flex items-center">
                            @include('livewire.common.label', ['title' => __('text-to-audio.select_voice')])
                        </div>
                        @if ($errors->has('selectedVoice'))
                        <span class="text-red-500 text-sm">{{ $errors->first('selectedVoice') }}</span>
                        @endif
                        <div class="overflow-auto">
                            @foreach($voices as $key => $voice)
                            <div
                                class="flex items-center justify-between px-4 py-2 border border-t-0 border-x-0 border-b">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <input value={{$voice['id']}} wire:model="selectedVoice" type="radio"
                                            name="voice"
                                            class="cursor-pointer border-zinc-500 checked:bg-secondary checked:hover:bg-secondary checked:active:bg-secondary checked:focus:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary" />
                                        <label class="text-zinc-500">{{$voice['label']}}</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="rounded-full @if($voice['meta']['gender'] === 'male') bg-blue-400 @endif @if($voice['meta']['gender'] === 'female') bg-secondary @endif px-3 py-0.5 text-white text-xs">
                                            {{$voice['meta']['gender']}}
                                        </div>
                                        <div class="rounded-full px-3 py-0.5 bg-gray-500 text-white text-xs">
                                            {{$voice['meta']['age']}}
                                        </div>
                                        @if($voice['meta']['description'] ?? false)
                                        <div class="rounded-full px-3 py-0.5 bg-gray-500 text-white text-xs">
                                            {{$voice['meta']['description']}}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div wire:click="playAudio('{{$voice['id']}}')">
                                    <x-icon solid name="play" width="30" height="30"
                                        class="cursor-pointer text-zinc-500" />
                                </div>
                                <audio id="{{ $voice['id'] }}" src="{{ $voice['url'] }}" preload="auto"></audio>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Input Text -->
                    <div class="flex flex-col w-full md:w-1/2 h-[20rem] md:h-full gap-4 py-4">
                        <div class="flex items-center">
                            @include('livewire.common.label', ['title' => __('text-to-audio.input_text')])
                        </div>
                        <textarea rows="15" wire:model="inputText" class="w-full p-4 border border-zinc-200 rounded-lg"
                            wire:model="inputText"></textarea>
                        @if ($errors->has('inputText'))
                        <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
                        @endif
                        <button :disabled="$isProcessing" wire:click="generate"
                            class="bg-secondary transition-colors ease-in-out duration-500 delay-150 hover:bg-main text-xl font-bold px-4 py-2 rounded-lg text-sm text-zinc-200">
                            <div class="py-1">
                                @if ($isProcessing)
                                <x-loader color="white" /> @else <span>{{__('text-to-audio.convert_to_audio')}}</span>
                                @endif
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- @if($selectedTab === 'history')
            history
            @endif --}}

        </div>
    </div>
</div>
@push('scripts')
@include('livewire.text-to-audio.audio-scripts')
@endpush
