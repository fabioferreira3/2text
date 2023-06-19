<div class="flex items-center gap-4">
    <div class="mr-4 font-bold">Tone:</div>
    <button wire:click="setTone(null)" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === null ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.default') }}</button>
    <button wire:click="setTone('simplistic')" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'simplistic' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.simple') }}</button>
    <button wire:click="setTone('formal')" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'formal' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.formal') }}</button>
    <button wire:click="setTone('funny')" class="duration-500 rounded-full  text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'funny' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.funny') }}</button>
</div>