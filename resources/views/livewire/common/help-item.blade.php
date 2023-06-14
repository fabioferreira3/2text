<div class="relative hidden md:block" x-data="{ open: false }" @click.away="open = false">
    <!-- This is your element that triggers the popover -->
    <button @click="open = true" class="h-8 2-8">
        <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" />
    </button>

    <!-- This is your popover -->
    <div class="absolute flex flex-col gap-4 bg-white border shadow-lg p-4 w-80 rounded-lg z-40" x-show="open" @mouseover="open = true" style="display: none;">
        <div class="flex items-center justify-between w-full">
            @isset($header) <span class="font-bold text-xl">{{$header}}</span> @endisset
            <x-button icon="x" sm @click="open = false"/>
        </div>
        <div>
            {!!$content!!}
        </div>
    </div>
</div>
