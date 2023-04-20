<nav class="flex flex-col min-h-screen">
    <div class="relative mb-4 h-full py-4">
        <div class="flex items-center">
            <a class="absolute" href="{{ route('dashboard') }}">
                <img src="/logo.png" class="w-3/4 h-full"/>
            </a>
        </div>
    </div>
    <div class="flex flex-col gap-5 mt-12">
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'dashboard', 'name' => 'Dashboard', 'icon' => 'home'])
        </div>
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'templates', 'name' => 'Templates', 'icon' => 'puzzle'])
        </div>
    </div>

</nav>
