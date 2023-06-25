<a href="{{ route($route) }}" class="flex gap-4 items-center {{request()->routeIs($activeRoutes ?? $route) ? 'font-bold text-secondary' : 'text-white'}}">
    <x-icon name={{$icon}} class="w-6 h-6" />
    <span class="xl:text-xl">{{$name}}</span>
</a>
