<div wire:click='execute' class="flex flex-col gap-3 p-6 border bg-white rounded-lg hover:border-secondary hover:cursor-pointer">
    <x-icon name={{$icon}} class="text-secondary w-16 h-16" />
    <div class="font-bold text-xl">{{ $title }}</div>
    <div>{{ $description }}</div>
</div>
