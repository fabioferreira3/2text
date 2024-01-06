<a href="{{ route($route) }}"
    class="flex gap-4 items-center {{request()->routeIs($activeRoutes ?? $route) ? 'font-bold text-secondary' : 'text-white hover:text-secondary transition duration-200'}}">
    <x-icon name={{$icon}} class="w-6 h-6" />
    <span class="{{$submenu ?? false ? " xl:text-base" : "xl:text-xl" }}">{{$name}}</span>
</a>
