<nav x-data="{ open: false }" class="w-full">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6 flex items-center md:block bg-main sm:bg-white">
        <div class="sm:hidden bg-main p-4">
            <a href="{{ route('home') }}">
                <img src="/logo.png" class="m-auto w-1/2 h-full" />
            </a>
        </div>
        <div class="flex w-full items-center justify-between h-12">
            <div class="mr-8 sm:mr-0">
                @if ((url()->current() !== url()->previous()))
                <a href="{{url()->previous()}}"
                    class="flex text-white text-sm items-center gap-2 bg-secondary px-4 py-1 rounded-full">
                    <x-icon name="arrow-circle-left" class="w-4 h-4" />
                    <span>{{__('common.back')}}</span>
                </a>
                @endif
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="font-medium text-gray-500 flex items-center gap-2">My units: <div
                        class="px-4 py-1 bg-gray-200 rounded-lg text-sm font-bold">
                        {{number_format(auth()->user()->account->units)}}</div>
                </div>
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                            @else
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                    {{ Auth::user()->name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('menus.manage_account') }}
                            </div>

                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('menus.profile') }}
                            </x-jet-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('menus.api_tokens') }}
                            </x-jet-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('menus.logout') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div class="md:hidden">
        <div :class="{'block bg-white': open, 'hidden': ! open}">
            <div class="pt-2 pb-3 space-y-1">
                <x-jet-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('dashboard')">
                    {{ __('menus.dashboard') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('tools') }}" :active="request()->routeIs('templates')">
                    {{ __('menus.tools') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('new-social-media-post') }}"
                    :active="request()->routeIs('new-social-media-post')">
                    {{ __('menus.social_media_post') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('new-post') }}" :active="request()->routeIs('new-post')">
                    {{ __('menus.blog_post') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('new-audio-transcription') }}"
                    :active="request()->routeIs('new-audio-transcription')">
                    {{ __('menus.audio_transcription') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('new-paraphraser') }}"
                    :active="request()->routeIs('new-paraphraser')">
                    {{ __('menus.paraphraser') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('new-text-to-audio') }}"
                    :active="request()->routeIs('new-text-to-audio')">
                    {{ __('menus.text_to_audio') }}
                </x-jet-responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <x-jet-responsive-nav-link href="{{ route('profile.show') }}"
                        :active="request()->routeIs('profile.show')">
                        {{ __('menus.profile') }}
                    </x-jet-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}"
                        :active="request()->routeIs('api-tokens.index')">
                        {{ __('menus.api_tokens') }}
                    </x-jet-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-jet-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('menus.logout') }}
                        </x-jet-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>

</nav>
