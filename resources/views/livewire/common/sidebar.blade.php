<nav class="flex flex-col h-full">
    <div class="relative mb-4 h-24">
        <div class="flex w-full items-center justify-center">
            <a href="{{ route('home') }}">
                <img src="/logo.png" class="w-full" />
            </a>
        </div>
    </div>
    <div class="flex flex-col gap-5 mt-6 3xl:mt-12">
        <div class=" hidden sm:flex">
            @include('livewire.common.navlink', [
            'route' => 'home',
            'activeRoutes' => ['home', 'trash'],
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
            'activeRoutes' => ['social-media-dashboard', 'new-social-media-post', 'social-media-view'],
            'route' => 'social-media-dashboard',
            'submenu' => true,
            'name' => __('menus.social_media_post'),
            'icon' => 'hashtag',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['blog-dashboard', 'new-post', 'blog-post-view'],
            'route' => 'blog-dashboard',
            'submenu' => true,
            'name' => __('menus.blog_post'),
            'icon' => 'newspaper',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['my-images'],
            'route' => 'my-images',
            'submenu' => true,
            'name' => __('menus.ai_images'),
            'icon' => 'photograph',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['paraphraser-dashboard', 'new-paraphraser', 'paraphrase-view'],
            'route' => 'paraphraser-dashboard',
            'submenu' => true,
            'name' => __('menus.paraphraser'),
            'icon' => 'switch-horizontal',
            ])
            @include('livewire.common.navlink', [
            'route' => 'new-text-to-audio',
            'submenu' => true,
            'name' => __('menus.text_to_audio'),
            'icon' => 'volume-up',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['transcription-dashboard', 'new-audio-transcription', 'transcription-view'],
            'route' => 'transcription-dashboard',
            'submenu' => true,
            'name' => __('menus.audio_transcription'),
            'icon' => 'chat-alt',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['summarizer-dashboard', 'new-summarizer', 'summary-view'],
            'route' => 'summarizer-dashboard',
            'submenu' => true,
            'name' => __('menus.summarizer'),
            'icon' => 'sort-ascending',
            ])
            @include('livewire.common.navlink', [
            'activeRoutes' => ['insight-dashboard', 'insight-view'],
            'route' => 'insight-dashboard',
            'submenu' => true,
            'name' => __('menus.insight_hub'),
            'icon' => 'search-circle',
            ])
        </div>
    </div>
    <div class="flex flex-col h-full w-full mt-4 3xl:mt-40">
        <div class="flex flex-col items-center gap-1 bg-gray-100 p-2 rounded-lg">
            @if(auth()->user()->sparkPlan())
            <div class="flex flex-col items-center">
                <div class="text-sm">{{__('menus.current_plan')}}:</div>
                <div class="font-bold text-lg">{{auth()->user()->sparkPlan()->name}}</div>
            </div>
            @endif
            <a class="flex items-center gap-2 bg-secondary px-4 py-2 rounded-lg text-white" href="/billing">
                <x-icon name="sparkles" width="24" height="24" />
                <div>@if(auth()->user()->sparkPlan()) {{__('menus.upgrade')}} @else {{__('menus.upgrade_plan')}} @endif
                </div>
            </a>
        </div>
    </div>
</nav>
