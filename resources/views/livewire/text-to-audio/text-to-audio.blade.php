<div class="flex flex-col g-white rounded-lg grow h-full">
    @section('header')
    <div class="flex items-center justify-between gap-8">
        <div class="flex items-center gap-2">
            @include('livewire.common.header', ['icon' => 'volume-up', 'title' => __('text-to-audio.text_to_audio')])
        </div>
        <div class="w-1/2">
            @include('livewire.common.page-info', ['content' => __('text-to-audio.page_info')])
        </div>
    </div>
    @endsection

    <!-- Tab Selector -->
    <div class="flex items-center text-zinc-700">
        <div wire:click="$set('selectedTab', 'new')" class="@if($selectedTab !== 'new') cursor-pointer
                    text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif flex items-center gap-2
                    border-t border-l border-zinc-200 border-b-0 hover:bg-zinc-100 rounded-tl-lg px-6 py-2">
            <x-icon name="volume-up" class="text-secondary" width="24" height="24" />
            <h2 class="text-lg">
                {{__('text-to-audio.create_audio')}}
            </h2>
        </div>
        <div wire:click="$set('selectedTab', 'my-audios')" class="@if($selectedTab !== 'my-audios') cursor-pointer
                    text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif
                    flex items-center gap-2 border border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 px-6 py-2">
            <x-icon name="volume-up" class="text-secondary" width="24" height="24" />
            <h2 class="text-lg">{{__('text-to-audio.my_audios')}}</h2>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-zinc-100 grow rounded-b-lg rounded-r-lg px-4 pb-4 pt-4 border border-zinc-200">
        @if ($selectedTab === 'new')
        <!-- Container -->

        <div class="flex flex-col md:flex-row gap-8 bg-white p-8 rounded-lg h-full">
            <!-- Voice selector -->
            <div class="flex flex-col gap-4 w-full md:w-1/2">
                <div class="flex items-center grow-0">
                    @include('livewire.common.label', ['title' => __('text-to-audio.select_voice')])
                </div>
                @if ($errors->has('selectedVoice'))
                <span class="text-red-500 text-sm">{{ $errors->first('selectedVoice') }}</span>
                @endif
                <div class="flex flex-col overflow-auto h-[1px] grow">
                    @foreach($voices as $key => $voice)
                    <div
                        class="{{$selectedVoice === $voice['id'] ? 'bg-gray-200' : ''}} flex items-center justify-between px-4 py-2 border border-t-0 border-x-0 border-b">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <input value={{$voice['id']}} wire:model="selectedVoice" type="radio" name="voice"
                                    class="cursor-pointer border-zinc-500 checked:bg-secondary checked:hover:bg-secondary checked:active:bg-secondary checked:focus:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary" />
                                <label class="text-zinc-500">{{$voice['label']}}</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <div
                                    class="rounded-full @if($voice['meta']['gender'] === 'male') bg-blue-400 @endif @if($voice['meta']['gender'] === 'female') bg-secondary @endif px-3 py-0.5 text-white text-xs">
                                    {{$voice['meta']['gender']}}
                                </div>
                            </div>
                        </div>
                        <div wire:click="playAudio('{{$voice['id']}}')"
                            class="flex items-center gap-1 border border-gray-200 bg-gray-100 px-3 py-1 rounded-lg cursor-pointer hover:bg-main group">
                            <x-icon solid name="play" width="40" height="40"
                                class="text-zinc-500 group-hover:text-white" />
                            <div class="text-sm text-gray-600 font-bold group-hover:text-white">
                                {{__('audio.sample')}}
                            </div>
                        </div>
                        <audio id="{{ $voice['id'] }}" src="{{ $voice['url'] }}" preload="auto"></audio>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Input Text -->
            <div class="flex flex-col gap-4 w-full md:w-1/2">
                <div class="flex items-center">
                    @include('livewire.common.label', ['title' => __('text-to-audio.input_text')])
                </div>
                <textarea rows="6" wire:model="inputText" class="w-full p-4 border border-zinc-200 rounded-lg"
                    wire:model="inputText"></textarea>
                @if ($errors->has('inputText'))
                <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
                @endif
                @if ($currentAudioFile && !$isProcessing)
                <div class="w-full mx-auto md:w-1/2 flex flex-col md:flex-row items-center justify-center gap-4">
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
                                                            rounded-lg text-sm text-white w-full"
                        wire:click='downloadAudio'>
                        <x-icon class="w-5 h-5" name="cloud-download" />
                        <div class="font-bold text-base">{{__('text-to-audio.download')}}</div>
                    </button>
                </div>
                @endif
                <button @if($isProcessing) disabled @endif wire:click="generate" class="w-2/3 xl:w-1/2 place-self-center
                                                bg-secondary transition-colors ease-in-out
                                                duration-500 delay-150 hover:bg-main text-xl font-bold px-4 py-2 rounded-lg
                                                text-sm text-zinc-200">

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

    @if($selectedTab === 'my-audios')
    @livewire('text-to-audio.audio-history', ['displayHeader' => false])
    @endif

</div>
</div>
@push('scripts')
@include('livewire.text-to-audio.audio-scripts')
@endpush
