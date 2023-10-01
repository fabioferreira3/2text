<div class="relative bg-white">
    <div class="relative">
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
            <textarea wire:click="displayBlockOptions" class="p-0 autoExpandTextarea w-full border-0 text-base"
                name="text" wire:model.debounce.500ms="content" wire:ignore></textarea>
            <div class="border border-b border-gray-50"></div>
            @if($showBlockOptions)
            <div class="flex items-center text-sm gap-2 absolute right-0 -top-8 bg-white">
                <button wire:click="shorten"
                    class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
                    <x-icon name="menu-alt-4" width="18" height="18" />
                    <div>shorten</div>
                </button>
                <button wire:click="expand"
                    class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150">
                    <x-icon name="menu" width="18" height="18" />
                    <div>expand</div>
                </button>
                <button
                    class="flex items-center gap-2 text-gray-600 hover:bg-secondary hover:text-white hover:border-white bg-gray-100 border border-gray-400 px-3 py-1 rounded-lg transition ease-in-out duration-200 hover:delay-150"
                    wire:click="toggleCustomPrompt">
                    <x-icon name="speakerphone" width="18" height="18" />
                    <div>Ask
                        to...</div>
                </button>
            </div>
            @endif
        </div>
        @endif
        @if($processing)
        <div class="bg-black opacity-20 flex items-center justify-center absolute top-0 left-0 w-full h-full">
            <x-loader height="8" width="8" color="white" />
        </div>
        @endif
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
