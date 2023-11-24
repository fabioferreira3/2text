<div class="w-full h-full">
    @section('header')
    @include('livewire.common.header', [
    'icon' => 'hashtag',
    'title' => 'New Social Media Post',
    'suffix' => '',
    ])
    @endsection

    <div class="flex mt-52 justify-center w-full">
        <button wire:click="redirectToCreationPage"
            class="flex items-center gap-2 bg-secondary text-white px-3 py-2 rounded-lg text-lg">
            <x-icon name="hashtag" color="white" width="28" height="28" />
            <span class="font-bold text-2xl">{{__('social_media.start_here')}}</span>
        </button>
    </div>
</div>
