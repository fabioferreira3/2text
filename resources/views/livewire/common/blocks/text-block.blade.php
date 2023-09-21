<div class="relative rounded-b-lg border border-zinc-200 p-4 bg-gray-100">
    <div class="flex mb-4 flex justify-end gap-2">
        <button wire:click="shorten" class="p1 font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">shorten</button>
        <button wire:click="expand" class="p1 font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">expand</button>
        <button wire:click="askTo" class="p1 font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg">ask to...</button>
    </div>
    @if ($processing)
    <div class="z-20 absolute top-0 left-0 bg-black opacity-20 h-full w-full"></div>
    <div class="z-30 absolute w-full h-full top-0 left-0 flex items-center justify-center">
        <x-loader height="20" width="20" color="white" />
    </div>
    @endif
    <textarea class="w-full text-base border-0 bg-gray-100 p-0" name="text" wire:model="content" rows="12"></textarea>
</div>