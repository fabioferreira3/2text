<div>
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'label' => __('paraphraser.paraphrase_text')])
    <div class="flex flex-col md:flex-row items-center justify-center gap-4 border-b py-4">
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
            @if ($outputBlocks)
            <div class="flex items-center justify-between gap-4">
                @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])

                <!-- Paraphrase options -->
                @if(!$isSaving)
                <div class="hidden lg:flex lg:flex-col xl:flex-row xl:items-center gap-2">
                    <button
                        class="flex items-center gap-2 bg-zinc-200 hover:text-zinc-200 hover:bg-zinc-500 px-4 py-2 rounded-lg text-sm text-zinc-600"
                        wire:click='copyAll'>
                        <x-icon class="w-5 h-5" :name="$copiedAll ? 'check' : 'clipboard-copy'" />
                        <div class="font-bold">{{$copiedAll ? __('common.copied') : __('common.copy')}}</div>
                    </button>
                    @livewire('common.generate-audio', ['document' => $document])
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
                    <textarea rows="10" class="w-full p-4 border border-zinc-200 rounded-lg"
                        wire:model="inputText"></textarea>
                    @if($errors->has('inputText'))
                    <span class="text-red-500 text-sm">{{ $errors->first('inputText') }}</span>
                    @endif
                </div>
                <div class="hidden w-full md:flex md:justify-center">
                    <button wire:target="paraphrase" :disabled="$isSaving"
                        class="mt-4 text-base bg-secondary duration-700 hover:bg-main text-white font-bold py-2 px-4 rounded-lg"
                        wire:click="paraphrase">
                        @if(!$isSaving)<span wire:target="paraphrase">{{
                            __('paraphraser.paraphrase') }}</span>@endif
                        @if($isSaving)<span wire:target="paraphrase">{{
                            __('paraphraser.processing') }}</span>@endif
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-2 relative">
                <div class="flex items-center justify-between md:hidden z-20">
                    @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])

                    <!-- Mobile paraphrase options -->
                    @if(!$isSaving)
                    <div class="md:hidden">
                        <x-custom.dropdown persistent direction="down">
                            @if ($selectedSentenceIndex !== null)
                            <x-dropdown.item icon="x">
                                <x-button sm wire:loading.attr="disabled" wire:click='unselect'
                                    label="{{__('common.unselect')}}"
                                    class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                            </x-dropdown.item>
                            @endif
                            <x-dropdown.item :icon="$copiedAll ? 'check' : 'clipboard-copy'">
                                <x-button sm wire:loading.attr="disabled" wire:click='copyAll'
                                    :label="$copiedAll ? __('common.copied') : __('common.copy')"
                                    class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                            </x-dropdown.item>
                        </x-custom.dropdown>
                    </div>
                    @endif
                </div>
                @if ($isSaving)
                <div class="absolute top-0 left-0 h-96 overflow-auto w-full bg-zinc-300 rounded-lg opacity-70 z-30">
                </div>
                <div class="z-40 absolute flex items-center justify-center h-full w-full">
                    <x-loader color="zinc-700" height="14" width="14" />
                </div>
                @endif
                <div class="relative h-full rounded-lg p-4">
                    @foreach ($outputBlocks as $contentBlock)
                    @livewire('common.blocks.text-block', [$contentBlock])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
