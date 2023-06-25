<div class="relative">
    <x-button md wire:click="toggle" :label="__('common.generate_audio')" icon="volume-up" class="hover:text-zinc-200 hover:bg-zinc-500 bg-zinc-200 text-zinc-500 border border-zinc-200 font-bold rounded-lg" />
    @if($menuOpen)
    <div class="text-zinc-600 overflow-auto flex flex-col absolute z-40 top-12 right-0 border rounded-lg w-64 text-sm bg-zinc-100">
        <div class="flex items-center justify-between w-full px-4 py-2 border-b bg-main text-white">
            <div class="font-bold">{{__('audio.choose_voice')}}:</div>
            <x-icon wire:click="toggle" solid name="x-circle" class="cursor-pointer w-5 h-5 text-white" />
        </div>
        <div class="max-h-64 overflow-auto bg-zinc-100">
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
        <x-button :disabled='$selectedVoice === null' neutral class="bg-secondary hover:bg-main" :label="$selectedVoice === null ? 'Select a voice' : 'Generate!'" />
    </div>
    @endif
</div>