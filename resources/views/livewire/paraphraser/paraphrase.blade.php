<div class="">
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'label' => __('paraphraser.paraphrase_text')])
    <div class="flex flex-col md:flex-row items-center justify-center gap-4 border-b py-4">
        <div class="flex items-center gap-4 justify-between w-full md:justify-start md:w-auto">
            <div class="mr-4 font-bold">Language:</div>
            <select name="language" wire:model="language" class="p-3 w-64 rounded-lg border border-zinc-200">
                @include('livewire.common.languages-options')
            </select>
        </div>
        <div>
            @include('livewire.paraphraser.tones')
        </div>
    </div>
    <div class="flex flex-col mt-4">
        <!-- Headers -->
        <div class="hidden md:grid md:grid-cols-2 gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('paraphraser.original_text')])
            </div>
            @if ($outputText)
                <div class="flex items-center justify-between gap-4">
                    @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])

                    <!-- Paraphrase options -->
                    @if(!$isSaving)
                        <div class="hidden lg:flex lg:flex-col xl:flex-row xl:items-center gap-2">
                            @if ($selectedSentenceIndex !== null)
                                <button class="flex items-center gap-2 bg-zinc-200 hover:text-zinc-200 hover:bg-zinc-500 px-4 py-2 rounded-lg text-sm text-zinc-600" wire:click='unselect'>
                                    <x-icon class="w-5 h-5" name="x"/>
                                    <div class="font-bold">{{__('common.unselect')}}</div>
                                </button>
                            @endif
                            <button class="flex items-center gap-2 bg-zinc-200 hover:text-zinc-200 hover:bg-zinc-500 px-4 py-2 rounded-lg text-sm text-zinc-600" wire:click='copyAll'>
                                <x-icon class="w-5 h-5" :name="$copiedAll ? 'check' : 'clipboard-copy'"/>
                                <div class="font-bold">{{$copiedAll ? __('common.copied') : __('common.copy')}}</div>
                            </button>
                            @livewire('common.generate-audio', ['document' => $document, 'text' => $document->paraphrased_text, 'language' => $language])
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Text areas -->
        <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-4 mt-2">
                <div class="md:hidden">
                    @include('livewire.common.label', ['title' => __('paraphraser.original_text')])
                </div>
                <div>
                    <textarea rows="10" class="w-full p-4 border border-zinc-200 rounded-lg" wire:model="inputText"></textarea>
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
            </div>
            @if ($outputText)
                <div class="flex flex-col gap-4 mt-2 relative">
                    <div class="flex items-center justify-between md:hidden z-20">
                        @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])

                        <!-- Mobile paraphrase options -->
                        @if(!$isSaving)
                            <div class="md:hidden">
                                <x-dropdown persistent>
                                    @if ($selectedSentenceIndex !== null)
                                    <x-dropdown.item icon="x">
                                            <x-button sm wire:loading.attr="disabled" wire:click='unselect' label="{{__('common.unselect')}}" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                                    </x-dropdown.item>
                                    @endif
                                    <x-dropdown.item :icon="$copiedAll ? 'check' : 'clipboard-copy'">
                                        <x-button sm wire:loading.attr="disabled" wire:click='copyAll' :label="$copiedAll ? __('common.copied') : __('common.copy')" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                                    </x-dropdown.item>
                                </x-dropdown>
                            </div>
                        @endif
                    </div>
                    @if ($isSaving)
                        <div class="absolute top-0 left-0 h-96 overflow-auto w-full bg-main rounded-lg opacity-70 z-50"></div>
                        <div class="absolute top-4 left-4 text-white h-full text-3xl z-50">Paraphrasing...</div>
                    @endif
                    <div class="relative border border-zinc-200 h-96 overflow-auto rounded-lg p-4">
                        @foreach ($outputText as $index => $sentence)
                            @if (trim($sentence['original']) !== '')
                                <div class="relative" x-data="{ open: false }" onclick="Livewire.emit('select', '{{ $index }}')" @click.away="open = false">
                                    <!-- This is your element that triggers the popover -->
                                    <p class="sentence cursor-pointer {{ $selectedSentenceIndex !== null && $selectedSentenceIndex === $index ? 'bg-main text-white rounded-lg p-3 my-2' : '' }}" @click="open = true" class="h-8 2-8">
                                        {{ $sentence['paraphrased'] }}
                                    </p>

                                    <!-- This is your popover -->
                                    @if (!$isSaving)
                                        <div class="absolute right-0 flex gap-2 bg-zinc-200 border border-zinc-300 shadow-lg rounded-full z-40" x-show="open" @mouseover="open = true" style="display: none;">
                                            <button sm wire:target="paraphraseSentence" wire:loading.attr="disabled" class="bg-secondary hover:bg-main text-white text-xs hover:font-bold rounded-full px-3 py-1" wire:click="paraphraseSentence">
                                                <span wire:target="paraphraseSentence" wire:loading.remove>Rephrase</span>
                                                <span wire:target="paraphraseSentence" wire:loading>Processing...</span>
                                            </button>
                                            <div @mouseenter="tooltip = true" @mouseleave="tooltip = false" x-data="{ tooltip: false }">
                                                <x-button.circle sm icon="reply" class="bg-zinc-300 border border-zinc-400 text-zinc-700 font-bold rounded-full" wire:click="resetSentence" />
                                                @include('livewire.tooltip', ['content' => __('common.undo')])
                                            </div>
                                            <div @mouseenter="tooltip = true" @mouseleave="tooltip = false" x-data="{ tooltip: false }">
                                                <x-button.circle sm :icon="$copied ? 'check' : 'clipboard-copy'" wire:click='copy' class="hover:text-zinc-700 bg-zinc-500 text-zinc-200 border border-zinc-200 font-bold rounded-full" />
                                                @include('livewire.tooltip', ['content' => $copied ? __('common.copied') : __('common.copy')])
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div wire:poll="checkCompleteness" class="hidden md:w-1/2 md:flex md:justify-center">
        <button wire:target="paraphraseAll,paraphraseSentence" :disabled="$isSaving" class="mt-4 text-base bg-secondary duration-700 hover:bg-main text-white font-bold py-2 px-4 rounded-lg" wire:click="paraphraseAll">
            @if(!$isSaving)<span wire:target="paraphraseAll,paraphraseSentence">{{ __('paraphraser.paraphrase') }}</span>@endif
            @if($isSaving)<span wire:target="paraphraseAll,paraphraseSentence">{{ __('paraphraser.paraphrasing') }}</span>@endif
        </button>
    </div>
</div>
