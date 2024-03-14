<div wire:click='execute'
    class="flex items-center xl:gap-0 md:gap-3 p-3 border-2 border-gray-100 bg-white rounded-lg hover:border-secondary hover:cursor-pointer transition duration-500">
    <x-icon name={{$icon}} class="text-secondary w-1/4 h-16" />
    <div class="flex flex-col w-3/4">
        <div class="font-bold text-xl text-gray-700">{{ $title }}</div>
        <div class="text-gray-600">{{ $description }}</div>
    </div>
</div>
