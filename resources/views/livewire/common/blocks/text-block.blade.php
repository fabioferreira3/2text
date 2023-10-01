<div class="relative bg-white">
    {{-- <div class="flex flex-col justify-end md:flex-row mb-4 gap-2">
        <button wire:click="shorten"
            class="p1 lg:text-sm xl:text-base font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">shorten</button>
        <button wire:click="expand"
            class="p1 lg:text-sm xl:text-base font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">expand</button>
        <button wire:click="toggleCustomPrompt"
            class="p1 lg:text-sm xl:text-base font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">ask
            to...</button>
    </div> --}}
    @if ($processing)
    <div class="z-20 absolute top-0 left-0 bg-black opacity-20 h-full w-full"></div>
    <div class="z-30 absolute w-full h-full top-0 left-0 flex items-center justify-center">
        <x-loader height="20" width="20" color="white" />
    </div>
    @endif
    @if (in_array($type, ['h2', 'h3', 'h4', 'h5', 'h6']))
    <input class="border-0 bg-white w-full font-bold p-0
        {{ $type === 'h2' ? 'text-2xl' : ''}}
        {{ $type === 'h3' ? 'text-xl' : ''}}
        {{ $type === 'h4' ? 'text-lg' : ''}}
        {{ $type === 'h5' ? 'text-base' : ''}}
        {{ $type === 'h6' ? 'text-base' : ''}}
        " name="text" wire:model.debounce.500ms="content" />
    @endif
    @if (in_array($type, ['paragraph']))
    <div class="relative group">
        <textarea class="autoExpandTextarea w-full border-0 text-base bg-white p-0" name="text"
            wire:model.debounce.500ms="content" wire:ignore></textarea>
        <div
            class="hidden group-hover:flex items-center gap-2 p-2 absolute right-0 top-0 bg-white rounded-lg border border-gray-200">
            <button class="bg-gray-200 px-3 py-1 rounded-lg">shorten</button>
            <button class="bg-gray-200 px-3 py-1 rounded-lg">expand</button>
            <button class="bg-gray-200 px-3 py-1 rounded-lg">Ask to...</button>
        </div>
    </div>
    @endif
    <div class="group/add flex items-center justify-center">
        <button class="invisible group-hover/add:visible text-3xl text-gray-400">+</button>
    </div>
    @if ($showCustomPrompt)
    <x-experior::modal>
        <div class="py-4 text-left px-6">
            <div role='button' class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold">Ask to...</p>
                <div role="button" class="cursor-pointer z-50" id="close" wire:click="toggleCustomPrompt">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 18 18">
                        <path
                            d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                        </path>
                    </svg>
                </div>
            </div>
            <input placeholder="ie: Rewrite this text but with a funnier tone"
                class="w-full text-base border-0 bg-gray-100 p-0 rounded-xl py-3 px-4" name="text"
                wire:model="customPrompt" />
            @if ($errors->has('customPrompt'))
            <span class="text-red-500 text-sm">{{ $errors->first('customPrompt') }}</span>
            @endif
            <div class="flex justify-start mt-8">
                <button wire:click="runCustomPrompt" wire:loading.remove
                    class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-xl">
                    <x-icon name="play" class="w-8 h-8" />
                    <span>{{ __('social_media.go') }}</span>
                </button>
            </div>
        </div>
    </x-experior::modal>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    let textareas = document.querySelectorAll('.autoExpandTextarea');

    function adjustHeight(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
    }

    textareas.forEach(textarea => {
    textarea.addEventListener('input', function () {
    adjustHeight(this);
    });

    // Initial adjustment
    adjustHeight(textarea);
    });
    });
</script>
