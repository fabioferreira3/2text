<div class="flex items-center gap-4 text-zinc-700 border-b border-zinc-200 pb-4">
    <x-icon name="{{$icon}}" class="w-14 h-14 text-secondary" />
    <div class="flex flex-col w-full">
        @if($editable ?? false) <input class="text-2xl md:text-4xl font-bold border-0 p-0" wire:model.lazy="title" />
        @else
        <h1 class="text-2xl md:text-4xl font-bold">
            {{$title}}
        </h1>@endif
        @isset($suffix)<h2 class="text-xl md:text-2xl font-bold">{{$suffix ?? ''}}</h2>@endisset
    </div>
</div>