<div class="flex items-center gap-4 text-zinc-700 border-b border-zinc-200 pb-4">
    <x-icon name="{{$icon}}" class="w-14 h-14 text-secondary" />
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl md:text-4xl font-bold">{{$label}}</h1>
        @isset($suffix)<h2 class="text-xk md:text-2xl font-bold">{{$suffix ?? ''}}</h2>@endisset
    </div>
</div>