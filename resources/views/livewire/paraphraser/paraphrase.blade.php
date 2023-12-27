<div>
    @section('header')
    @livewire('common.header', [
    'icon' => 'switch-horizontal',
    'title' => $document->title ?? __('paraphraser.paraphrase_text'),
    'editable' => true,
    'document' => $document
    ])
    {{-- @include('livewire.common.header', ['icon' => 'switch-horizontal', 'title' =>
    __('paraphraser.paraphrase_text')]) --}}
    @endsection
    <div class="flex flex-col md:flex-row items-center justify-center gap-4 border-b py-4">
        <div>
            @include('livewire.paraphraser.tones', ['tone' => $tone])
        </div>
    </div>
    <div class="flex flex-col mt-4">
        <!-- Headers -->
        <div class="hidden md:grid md:grid-cols-2 gap-4 py-4">
            <div class="flex items-center">
                @include('livewire.common.label', ['title' => __('paraphraser.original_text')])
            </div>

            <div class="flex items-center justify-between gap-4">
                @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])

                <!-- Paraphrase options -->
                @if(count($outputBlocks) && !$isSaving)
                <div class="hidden lg:flex lg:flex-col xl:flex-row xl:items-center gap-2">
                    <button class="flex items-center gap-2 bg-zinc-200 hover:text-zinc-200 hover:bg-zinc-500
                            px-4 py-2 rounded-lg text-sm text-zinc-600" wire:click='copy'>
                        <x-icon class="w-5 h-5" :name="$copied ? 'check' : 'clipboard-copy'" />
                        <div class="font-bold">{{$copied ? __('common.copied') : __('common.copy')}}</div>
                    </button>
                </div>
                @endif
            </div>
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
                    <button wire:target="paraphrase" @if($isSaving) disabled @endif class="mt-4 text-base bg-secondary duration-700 hover:bg-main
                        text-white font-bold py-2 px-4 rounded-lg" wire:click="paraphrase">
                        @if(!$isSaving)<span wire:target="paraphrase">{{
                            __('paraphraser.paraphrase') }}</span>@endif
                        @if($isSaving)<span wire:target="paraphrase">{{
                            __('paraphraser.processing') }}</span>@endif
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-2 relative h-[25rem]">
                <div class="flex items-center justify-between md:hidden z-20">
                    @include('livewire.common.label', ['title' => __('paraphraser.paraphrased_text')])
                </div>
                @if ($isSaving)
                <div class="z-40 absolute flex items-center justify-center h-full w-full">
                    <x-loader color="zinc-700" height="14" width="14" />
                </div>
                @endif
                <div class="relative h-full rounded-lg p-4 overflow-auto h-full">
                    @foreach ($outputBlocks as $contentBlock)
                    @livewire('common.blocks.text-block', [$contentBlock], key($contentBlock->id))
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function adjustTextArea() {
        let textareas = document.querySelectorAll('.autoExpandTextarea');

        function adjustHeight(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                adjustHeight(this);
            });

            // Initial adjustment
            adjustHeight(textarea);
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        adjustTextArea();
    });

    document.addEventListener('adjustTextArea', function() {
        adjustTextArea();
    });
</script>
@endpush