<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'volume-up', 'label' => $document ? __('text-to-speech.text_to_audio') : __('text-to-speech.new_text_to_audio')])
    <div class="flex flex-col md:flex-row items-center justify-center gap-4 border-b py-4">
        <div class="flex items-center gap-4 justify-between w-full md:justify-start md:w-auto">
            <div class="mr-4 font-bold">Language:</div>
            <select name="language" wire:model="language" wire:change="changeLanguage" class="p-3 w-64 rounded-lg border border-zinc-200">
                <option value="en">English</option>
                <option value="es">Spanish</option>
                <option value="pt">Portuguese</option>
            </select>
        </div>
    </div>
    @if ($currentAudioFile && !$isProcessing)
    <div class="w-full m-auto md:w-1/3 flex flex-col items-center justify-center md:flex-row gap-1">
        <button wire:click="processAudio('listen_current_audio')" class="transition-colors ease-in-out duration-500 delay-150 flex items-center justify-center gap-2 bg-secondary border border-secondary hover:bg-zinc-200 hover:text-zinc-700 hover:border hover:border-zinc-300 py-2 px-3 rounded-lg text-sm text-white w-full" wire:click='toggle'>
            <x-icon class="w-5 h-5" name="volume-up" />
            <div class="font-bold text-base">{{$isPlaying ? __('common.stop') : __('common.listen')}}</div>
        </button>
        <audio id="listen_current_audio" src="{{ $currentAudioUrl }}" preload="auto"></audio>
        <button class="transition-colors ease-in-out duration-500 delay-150 flex items-center justify-center gap-2 bg-main border border-main hover:bg-zinc-200 hover:text-zinc-700 hover:border hover:border-zinc-300 py-2 px-3 rounded-lg text-sm text-white w-full" wire:click='downloadAudio'>
            <x-icon class="w-5 h-5" name="cloud-download" />
            <div class="font-bold text-base">{{__('common.download')}}</div>
        </button>
    </div>
    @endif
    <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
        <div class="flex flex-col gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('text-to-speech.select_voice')])
            </div>
            @if ($errors->has('selectedVoice'))
            <span class="text-red-500 text-sm">{{ $errors->first('selectedVoice') }}</span>
            @endif
            <div class="h-48 md:h-full overflow-auto">
                @foreach($voices as $key => $voice)
                <div class="flex items-center justify-between px-4 py-2 border border-t-0 border-x-0 border-b">
                    <div class="flex items-center gap-2">
                        <input value={{$voice['value']}} wire:model="selectedVoice" type="radio" name="voice" class="cursor-pointer border-zinc-500 checked:bg-secondary checked:hover:bg-secondary checked:active:bg-secondary checked:focus:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary" />
                        <label class="text-zinc-500">{{$voice['label']}}</label>
                    </div>
                    <div wire:click="playAudio('{{$voice['id']}}')">
                        <x-icon solid name="play" class="cursor-pointer w-5 h-5 text-zinc-500" />
                    </div>
                    <audio id="{{ $voice['id'] }}" src="{{ $voice['url'] }}" preload="auto"></audio>
                </div>
                @endforeach
            </div>
        </div>
        <div class="flex flex-col gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('text-to-speech.input_text')])
            </div>
            <textarea rows="10" wire:model="inputText" class="w-full p-4 border border-zinc-200 rounded-lg" wire:model="inputText"></textarea>
            @if ($errors->has('inputText'))
            <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
            @endif
            <button :disabled="$isProcessing" wire:click="generate" class="bg-secondary transition-colors ease-in-out duration-500 delay-150 hover:bg-main text-xl font-bold px-4 py-2 rounded-lg text-sm text-zinc-200">
                <div class="py-1">
                    @if ($isProcessing) <x-loader color="white" /> @else <span>Generate audio</span> @endif
                </div>
            </button>
        </div>
    </div>
</div>