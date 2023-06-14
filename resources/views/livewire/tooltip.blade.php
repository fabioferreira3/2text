<div
    class="absolute px-2 py-1 mt-2 text-xs text-white bg-black rounded"
    x-show="tooltip"
    style="display: none;"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
>
    {{$content}}
</div>
