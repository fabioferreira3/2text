<div class="relative">
    <div class="cursor-pointer hover:text-zinc-200 hover:bg-zinc-500 bg-zinc-200 text-zinc-500 py-1.5 px-3 border border-zinc-200 font-bold rounded-lg">
        <div class="text-sm">{{__('common.generate_audio')}}</div>
    </div>
    <div class="text-zinc-600 overflow-auto flex flex-col absolute top-10 right-0 border rounded-lg w-64 text-sm bg-zinc-100">
        <div class="flex justify-between w-full px-2 py-1 border-b bg-white">
            <div class="font-bold">{{__('audio.choose_voice')}}:</div>
            <x-icon solid name="x-circle" class="cursor-pointer w-5 h-5 text-zinc-500" />
        </div>
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
        <x-button :disabled='$selectedVoice === null' neutral class="bg-secondary hover:bg-main" label="Generate!"/>
    </div>

</div>
