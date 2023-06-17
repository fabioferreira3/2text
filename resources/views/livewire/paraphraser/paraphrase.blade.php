<div class="flex flex-col gap-8">
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'label' => __('paraphraser.paraphrase_text')])
    <div class="flex flex-col md:flex-row items-center justify-center gap-4 border-b-2 pb-4">
        <div class="mr-4 font-bold">Tone:</div>
        <button wire:click="setTone(null)" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === null ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.default') }}</button>
        <button wire:click="setTone('simple')" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'simple' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.simple') }}</button>
        <button wire:click="setTone('formal')" class="duration-500 rounded-full text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'formal' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.formal') }}</button>
        <button wire:click="setTone('funny')" class="duration-500 rounded-full  text-sm px-4 py-1 hover:bg-main hover:text-white hover:font-bold {{$tone === 'funny' ? 'bg-main text-white font-bold' : 'bg-zinc-100 text-zinc-700'}}">{{ __('paraphraser.funny') }}</button>
    </div>
    <div class="flex flex-col md:flex-row md:mt-4">
        <div class="w-full md:w-1/2 p-4">
            @include('livewire.common.label', ['title' => __('paraphraser.original_text')])
            <textarea class="mt-6 w-full h-80 md:h-full p-4 border border-zinc-200 rounded-lg" wire:model="inputText"></textarea>
            @if($errors->has('inputText'))
            <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
            @endif
        </div>
        <div class="md:hidden border-b w-full py-6 flex justify-center">
            <button class="md:mt-4 flex items-center gap-2 bg-secondary duration-700 hover:bg-main text-white font-bold py-2 px-4 rounded-lg" wire:click="paraphraseAll">
                <x-icon name="arrow-circle-down" class="w-5 h-5" />
                <span>{{ __('paraphraser.paraphrase') }}</span>
            </button>
        </div>
        <div class="w-full md:w-1/2 p-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])
                <div class="flex items-center gap-2">
                    @if ($selectedSentenceIndex !== null) <x-button sm label="{{__('common.unselect')}}" icon="x" wire:click='unselect' class="hover:text-zinc-200 hover:bg-zinc-500 bg-zinc-200 text-zinc-500 border border-zinc-200 font-bold rounded-lg" /> @endif
                    <x-button sm :label="$copiedAll ? __('common.copied') : __('common.copy')" :icon="$copiedAll ? 'check' : 'clipboard-copy'" wire:click='copyAll' class="hover:text-zinc-200 hover:bg-zinc-500 bg-zinc-200 text-zinc-500 border border-zinc-200 font-bold rounded-lg" />
                </div>
            </div>
            <div class="h-full border border-zinc-200 rounded-lg p-4 mt-4">
                @foreach ($outputText as $index => $sentence)
                @if (trim($sentence['original']) !== '')
                <div class="relative" x-data="{ open: false }" onclick="Livewire.emit('select', '{{ $index }}')" @click.away="open = false">
                    <!-- This is your element that triggers the popover -->
                    <p class="sentence cursor-pointer {{ $selectedSentenceIndex !== null && $selectedSentenceIndex === $index ? 'bg-main text-white rounded-lg p-3 my-2' : '' }}" @click="open = true" class="h-8 2-8">
                        {{ $sentence['paraphrased'] }}
                    </p>

                    <!-- This is your popover -->
                    <div class="absolute right-0 flex gap-2 bg-zinc-200 border border-zinc-300 shadow-lg rounded-full z-40" x-show="open" @mouseover="open = true" style="display: none;">
                        <button sm class="bg-secondary hover:bg-main text-white text-xs hover:font-bold rounded-full px-3 py-1" wire:click="paraphraseSentence">Rephrase</button>
                        <div @mouseenter="tooltip = true" @mouseleave="tooltip = false" x-data="{ tooltip: false }">
                            <x-button.circle sm icon="reply" class="bg-zinc-300 border border-zinc-400 text-zinc-700 font-bold rounded-full" wire:click="resetSentence" />
                            @include('livewire.tooltip', ['content' => __('common.undo')])
                        </div>
                        <div @mouseenter="tooltip = true" @mouseleave="tooltip = false" x-data="{ tooltip: false }">
                            <x-button.circle sm :icon="$copied ? 'check' : 'clipboard-copy'" wire:click='copy' class="hover:text-zinc-700 bg-zinc-500 text-zinc-200 border border-zinc-200 font-bold rounded-full" />
                            @include('livewire.tooltip', ['content' => $copied ? __('common.copied') : __('common.copy')])
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    <div wire:loading.class="md:flex" class="hidden md:justify-center w-full p-4">
        <x-button spinner disabled label="{{ __('paraphraser.paraphrasing') }}" class="mt-4 text-base bg-secondary duration-700 text-white font-bold py-2 px-4 rounded-lg" />
    </div>
    <div wire:loading.remove class="hidden md:flex md:justify-center w-full p-4">
        <button class="mt-4 text-base bg-secondary duration-700 hover:bg-main text-white font-bold py-2 px-4 rounded-lg" wire:click="paraphraseAll">{{ __('paraphraser.paraphrase') }}</button>
    </div>
</div>