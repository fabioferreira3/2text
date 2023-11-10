<div>
    <div class="flex flex-col">
        @include('livewire.common.header', [
        'icon' => 'hashtag',
        'label' =>
        'New Social Media Post',
        'suffix' => '',
        ])
    </div>
    <div class="mt-8">
        <button wire:click="redirectToCreationPage"
            class="flex items-center gap-2 bg-secondary text-white px-3 py-2 rounded-lg text-lg">
            <x-icon name="hashtag" color="white" width="24" height="24" />
            <span class="font-bold">Click here to start</span>
        </button>
    </div>
</div>