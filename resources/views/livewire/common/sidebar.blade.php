<nav class="flex flex-col min-h-screen">
    <div class="relative mb-4 h-full">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="/logo.png" class="w-full h-full" />
            </a>
        </div>
    </div>
    <div class="flex flex-col gap-5 mt-12">
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'dashboard', 'activeRoutes' => ['dashboard', 'trash'], 'name' => __('menus.dashboard'), 'icon' => 'home'])
        </div>
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'templates', 'name' => __('menus.templates'), 'icon' => 'puzzle'])
        </div>
    </div>

</nav>
