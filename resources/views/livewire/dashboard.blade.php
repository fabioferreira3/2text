<div>
    <div class="w-full mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'desktop-computer', 'label' => __('dashboard.dashboard')])
        <livewire:common.create-document />
    </div>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="flex items-center text-zinc-700">
            <div wire:click="$set('selectedTab', 'dashboard')"
                class="@if($selectedTab !== 'dashboard') cursor-pointer text-zinc-500 @else bg-zinc-100 font-bold @endif flex items-center gap-2 border-t border-l border-zinc-200 border-b-0 hover:bg-zinc-100 rounded-tl-lg px-6 py-2">
                <x-icon name="document-text" class="text-zinc-600" width="24" height="24" />
                <h2 class="text-lg">
                    My Documents</h2>
            </div>
            <div wire:click="$set('selectedTab', 'images')"
                class="@if($selectedTab !== 'images') cursor-pointer text-zinc-500 @else bg-zinc-100 font-bold @endif flex items-center gap-2 border border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 rounded-tr-lg px-6 py-2">
                <x-icon name="photograph" class="text-zinc-500" width="24" height="24" />
                <h2 class="text-lg">My Images</h2>
            </div>
        </div>
        <div class="bg-zinc-100 rounded-b-lg rounded-r-lg px-4 pb-4 pt-4 border border-zinc-200">
            @if ($selectedTab === 'dashboard')
            <livewire:my-documents-table />
            @endif

            @if($selectedTab === 'images')
            <livewire:my-images />
            @endif
        </div>

    </div>
