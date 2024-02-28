<div>
    @section('header')
    <div class="w-full flex items-center justify-between">
        @livewire('common.header', ['icon' => 'desktop-computer', 'title' => $title ??
        __('dashboard.dashboard')])
    </div>
    @endsection
    <div class="flex flex-col bg-white rounded-lg">
        <!-- Desktop tabs -->
        <div class="hidden md:flex items-center text-zinc-700">
            <div wire:click="$set('tab', 'dashboard')" class="@if($tab !== 'dashboard') cursor-pointer
                text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif flex items-center gap-2
                border-t border-l border-zinc-200 border-b-0 hover:bg-zinc-100 rounded-tl-lg px-6 py-2">
                <x-icon name="document-text" class="text-secondary" width="24" height="24" />
                <h2 class="text-lg">
                    {{__('dashboard.my_documents')}}</h2>
            </div>
            <div wire:click="$set('tab', 'images')" class="@if($tab !== 'images') cursor-pointer
                text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif
                flex items-center gap-2 border border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 px-6 py-2">
                <x-icon name="photograph" class="text-secondary" width="24" height="24" />
                <h2 class="text-lg">{{__('dashboard.my_images')}}</h2>
            </div>
            <div wire:click="$set('tab', 'audio')" class="@if($tab !== 'audio') cursor-pointer
                text-zinc-500 @else bg-zinc-100 text-secondary font-bold @endif flex items-center gap-2 border-t
                border-r border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 rounded-tr-lg px-6 py-2">
                <x-icon name="volume-up" class="text-secondary" width="24" height="24" />
                <h2 class="text-lg">{{__('dashboard.my_audios')}}</h2>
            </div>
        </div>
        <!-- End: Desktop tabs -->

        <!-- Mobile tabs -->
        <div class="md:hidden relative">
            <div
                class="flex items-center justify-between bg-secondary px-8 py-2 w-full bg-zinc-100 border border-zinc-200 hover:bg-zinc-100 rounded-t-lg">
                <div class="text-secondary font-bold place-self-start flex items-center gap-2">
                    <x-icon name="document-text" class="text-secondary" width="24" height="24" />
                    <h2 class="text-lg">
                        @if($tab === 'dashboard') {{__('dashboard.my_documents')}}@endif
                        @if($tab === 'images') {{__('dashboard.my_images')}}@endif
                        @if($tab === 'audio') {{__('dashboard.my_audios')}}@endif
                    </h2>
                </div>
                <div wire:click="$toggle('mobileMenuOpen')" class="text-secondary">
                    <x-icon name="menu" width="24" height="24" />
                </div>
            </div>
            @if($mobileMenuOpen)
            <div class="absolute z-50 top-12 bg-white w-full border border-zinc-200 rounded-b-lg">
                <div wire:click="$set('tab', 'dashboard')"
                    class="flex items-center gap-2 px-8 py-3 border-b border-zinc-200 hover:bg-secondary hover:text-white hover:border-x hover:border-zinc-200">
                    <x-icon name="document-text" width="24" height="24" />
                    <h2 class="text-lg">
                        {{__('dashboard.my_documents')}}</h2>
                </div>
                <div wire:click="$set('tab', 'images')"
                    class="flex items-center gap-2 px-8 py-3 border-b border-zinc-200 hover:bg-secondary hover:text-white hover:border-x hover:border-zinc-200">
                    <x-icon name="document-text" width="24" height="24" />
                    <h2 class="text-lg">
                        {{__('dashboard.my_images')}}</h2>
                </div>
                <div wire:click="$set('tab', 'audio')"
                    class="flex items-center gap-2 px-8 py-3 rounded-b-lg hover:bg-secondary hover:text-white hover:border-x hover:border-zinc-200">
                    <x-icon name="document-text" width="24" height="24" />
                    <h2 class="text-lg">
                        {{__('dashboard.my_audios')}}</h2>
                </div>
            </div>
            @endif
        </div>
        <!-- End: Mobile tabs -->

        <div class="bg-zinc-100 rounded-b-lg md:rounded-r-lg md:px-4 pb-4 pt-4 border border-zinc-200">
            @if ($tab === 'dashboard')
            <livewire:my-documents-table />
            @endif

            @if($tab === 'images')
            <livewire:my-images />
            @endif

            @if($tab === 'audio')
            @livewire('text-to-audio.audio-history', ['displayHeader' => false])
            @endif
        </div>
    </div>
    @push('scripts')
    @include('livewire.text-to-audio.audio-scripts')
    @endpush