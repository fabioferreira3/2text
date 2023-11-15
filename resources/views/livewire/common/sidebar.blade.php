<nav class="flex flex-col min-h-screen">
    <div class="relative mb-4 h-24">
        <div class="flex w-full items-center justify-center">
            <a href="{{ route('dashboard') }}">
                <img src="/logo.png" class="w-full" />
            </a>
        </div>
    </div>
    <div class="flex flex-col gap-5 mt-12">
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', [
            'route' => 'dashboard',
            'activeRoutes' => ['dashboard', 'trash'],
            'name' => __('menus.dashboard'),
            'icon' => 'home',
            ])
        </div>
        <div class="hidden sm:flex">
            @include('livewire.common.navlink', [
            'route' => 'tools',
            'name' => __('menus.tools'),
            'icon' => 'puzzle',
            ])
        </div>
        <div class="hidden sm:flex sm:flex-col sm:gap-3 ml-4">
            @include('livewire.common.navlink', [
            'route' => 'create-social-media-post',
            'submenu' => true,
            'name' => __('menus.social_media_post'),
            'icon' => 'hashtag',
            ])
            @include('livewire.common.navlink', [
            'route' => 'new-post',
            'submenu' => true,
            'name' => __('menus.blog_post'),
            'icon' => 'newspaper',
            ])
            @include('livewire.common.navlink', [
            'route' => 'new-audio-transcription',
            'submenu' => true,
            'name' => __('menus.audio_transcription'),
            'icon' => 'chat-alt',
            ])
            @include('livewire.common.navlink', [
            'route' => 'new-paraphraser',
            'submenu' => true,
            'name' => __('menus.paraphraser'),
            'icon' => 'switch-horizontal',
            ])
            @include('livewire.common.navlink', [
            'route' => 'new-text-to-speech',
            'submenu' => true,
            'name' => __('menus.text_to_audio'),
            'icon' => 'volume-up',
            ])
        </div>
    </div>
</nav>
