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
    <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('text-to-speech.input_text')])
            </div>
            <textarea rows="10" wire:model="inputText" class="w-full p-4 border border-zinc-200 rounded-lg" wire:model="inputText"></textarea>
            @if($errors->has('inputText'))
                <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
            @endif
            <button wire:click="generate" class="bg-secondary transition-colors ease-in-out duration-500 delay-150 hover:bg-main text-xl font-bold px-4 py-2 rounded-lg text-sm text-zinc-200">
                Generate audio
            </button>
        </div>
        <div class="flex flex-col gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('text-to-speech.select_voice')])
            </div>
            <div>
                @foreach($voices as $key => $voice)
                    <div class="flex items-center justify-between px-4 py-2 border border-t-0 border-x-0 border-b">
                        <div class="flex items-center gap-2">
                            <input value={{$voice['value']}} wire:model="selectedVoice" type="radio" name="garai" class="cursor-pointer border-zinc-500 checked:bg-secondary checked:hover:bg-secondary checked:active:bg-secondary checked:focus:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary" />
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
    </div>
</div>
