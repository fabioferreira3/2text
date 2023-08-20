<div class="flex" x-data="{}" x-on:click.away="window.livewire.emit('toggle-audio-menu')">
    <div class="relative">
    <button class="flex items-center gap-2 bg-zinc-200 hover:text-zinc-200 hover:bg-zinc-500 px-4 py-2 rounded-lg text-sm text-zinc-600" wire:click='toggle'>
        <x-icon class="w-5 h-5" name="volume-up"/>
        <div class="font-bold">{{__('common.generate_audio')}}</div>
    </button>
        @if($menuOpen)
            <div class="text-zinc-600 overflow-auto flex flex-col absolute z-40 top-12 right-0 border rounded-lg w-64 text-sm bg-zinc-100">
                <div class="flex items-center justify-between w-full px-4 py-2 border-b bg-main text-white">
                    <div class="font-bold">{{__('audio.choose_voice')}}:</div>
                    <x-icon wire:click="toggle" solid name="x-circle" class="cursor-pointer w-5 h-5 text-white" />
                </div>
                @if ($isProcessing)
                    <div class="py-4">
                        <x-loader/>
                    </div>
                @endif
                <select name="language" wire:model="language" class="px-3 py-2 w-64 border-none w-full">
                    @include('livewire.common.voice-languages-options')
                </select>

                @if ($currentAudioFile && !$isProcessing)
                    <div class="flex flex-col gap-1 px-4 py-2">
                        <button wire:click="processAudio('listen_current_audio')" class="flex items-center gap-2 bg-secondary border border-secondary hover:bg-zinc-200 hover:text-zinc-700 hover:border hover:border-zinc-300 py-1 px-3 rounded-lg text-sm text-white w-full" wire:click='toggle'>
                            <x-icon class="w-5 h-5" name="volume-up"/>
                            <div class="font-bold">{{$isPlaying ? __('common.stop') : __('common.listen')}}</div>
                        </button>
                        <audio id="listen_current_audio" src="{{ $currentAudioUrl }}" preload="auto"></audio>
                        <button class="flex items-center gap-2 bg-main border border-main hover:bg-zinc-200 hover:text-zinc-700 hover:border hover:border-zinc-300 py-1 px-3 rounded-lg text-sm text-white w-full" wire:click='downloadAudio'>
                            <x-icon class="w-5 h-5" name="cloud-download"/>
                            <div class="font-bold">{{__('common.download')}}</div>
                        </button>
                    </div>
                @endif
                <div class="max-h-64 overflow-auto bg-zinc-100">

                    @foreach($voices as $key => $voice)
                    <div class="flex items-center justify-between px-4 py-2 border border-t-0 border-x-0 border-b">
                        <div class="flex items-center gap-2">
                            <input value={{$voice['value']}} wire:model="selectedVoice" type="radio" class="cursor-pointer border-zinc-500 checked:bg-secondary checked:hover:bg-secondary checked:active:bg-secondary checked:focus:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary" />
                            <label class="text-zinc-500">{{$voice['label']}}</label>
                        </div>
                        <div wire:click="playAudio('{{$voice['id']}}')">
                            <x-icon solid name="play" class="cursor-pointer w-5 h-5 text-zinc-500" />
                        </div>
                        <audio id="{{ $voice['id'] }}" src="{{ $voice['url'] }}" preload="auto"></audio>
                    </div>
                    @endforeach
                </div>
                <x-button wire:click="generate" :disabled='$selectedVoice === null || $isProcessing' neutral class="bg-secondary hover:bg-main" :label="$isProcessing ? 'Processing' : 'Generate'" />
            </div>
        @endif
    </div>
</div>
