<div wire:click='execute'
    class="flex flex-col gap-3 p-6 border-2 border-gray-100 bg-white rounded-lg hover:border-secondary hover:cursor-pointer transition duration-500">
    <x-icon name={{$icon}} class="text-secondary w-16 h-16" />
    <div class="font-bold text-xl text-gray-700">{{ $title }}</div>
    <div class="text-gray-600">{{ $description }}</div>
</div>
