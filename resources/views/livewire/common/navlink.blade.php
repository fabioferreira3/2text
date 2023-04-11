<a href="{{ route($route) }}" class="flex gap-2 items-center {{request()->routeIs($activeRoutes ?? $route) ? 'font-bold text-red-700' : 'text-zinc-700'}}">
    <x-icon name={{$icon}} class="w-6 h-6" />
    <span class="text-lg">{{ __($name) }}</span>
</a>

