<div class="w-full h-full">
    <div class="flex flex-col">
        @include('livewire.common.header', [
        'icon' => 'hashtag',
        'label' =>
        'New Social Media Post',
        'suffix' => '',
        ])
    </div>
    <div class="flex mt-52 justify-center w-full">
        <button wire:click="redirectToCreationPage" class="flex items-center gap-2 bg-secondary text-white px-3 py-2 rounded-lg text-lg">
            <x-icon name="hashtag" color="white" width="32" height="32" />
            <span class="font-bold text-3xl">{{__('social_media.start_here')}}</span>
        </button>
    </div>
</div>