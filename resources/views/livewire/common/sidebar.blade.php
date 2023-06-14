<nav class="flex flex-col min-h-screen">
    <div class="relative mb-4 h-24">
        <div class="flex items-center justify-center">
            <a href="{{ route('dashboard') }}">
                <img src="/logo.png" class="xl:w-2/3" />
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
