<div>
    <div class="w-full mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'desktop-computer', 'label' => __('dashboard.dashboard')])
        <livewire:common.create-document />
    </div>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="flex items-center text-zinc-700">
            <div wire:click="$set('selectedTab', 'dashboard')"
                class="@if($selectedTab !== 'dashboard') cursor-pointer text-zinc-500 @else bg-zinc-100 font-bold @endif flex items-center gap-2 border-t border-l border-zinc-200 border-b-0 hover:bg-zinc-100 rounded-tl-lg px-4 py-2">
                <x-icon name="document-text" class="text-zinc-600" width="24" height="24" />
                <h2 class="text-lg">
                    My Documents</h2>
            </div>
            <div wire:click="$set('selectedTab', 'images')"
                class="@if($selectedTab !== 'images') cursor-pointer text-zinc-500 @else bg-zinc-100 font-bold @endif flex items-center gap-2 border border-tr-zinc-400 border-b-0 bg-white hover:bg-zinc-100 rounded-tr-lg px-4 py-2">
                <x-icon name="photograph" class="text-zinc-500" width="24" height="24" />
                <h2 class="text-lg">My Images</h2>
            </div>
        </div>
        <div class="bg-zinc-100 rounded-b-lg rounded-r-lg px-4 pb-4 pt-8 border border-zinc-200">
            @if ($selectedTab === 'dashboard')
            <livewire:my-documents-table />
            @endif

            @if($selectedTab === 'images')
            @if(count($images))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($images as $image)
                <div class="h-[300px] relative">
                    <img wire:click="selectImage('{{$image->id}}')" src={{$image->file_url}} class="rounded-lg w-full
                    h-full object-cover" loading="lazy"/>
                    <div
                        class="transition-opacity duration-200 ease-in-out opacity-0 hover:opacity-60 absolute top-0 left-0 bg-black w-full h-full rounded-lg">
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @endif
        </div>
        @if ($selectedImage)
        <x-experior::modal>
            <div class="flex items-center justify-end mb-4">
                <div role="button" class="cursor-pointer z-50" id="close" wire:click="selectImage(null)">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        viewBox="0 0 18 18">
                        <path
                            d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z">
                        </path>
                    </svg>
                </div>
            </div>
            <img src="{{$selectedImage->file_url}}" />
        </x-experior::modal>
        @endif
    </div>
