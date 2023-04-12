<nav class="flex flex-col gap-12 rounded-lg min-h-screen">
    <div class="w-2/3">
        <div class="flex items-center">
            <a class="h-full" href="{{ route('dashboard') }}">
                <x-jet-application-mark class="block h-9 w-auto" />
            </a>
        </div>
    </div>
    <div class="flex flex-col gap-5">
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'dashboard', 'name' => 'Dashboard', 'icon' => 'home'])
        </div>
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'templates', 'name' => 'Templates', 'icon' => 'puzzle'])
        </div>
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', ['route' => 'pending-jobs', 'name' => 'My Queue', 'icon' => 'view-boards'])
        </div>
    </div>

</nav>
