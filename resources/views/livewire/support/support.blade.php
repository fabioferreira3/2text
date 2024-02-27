<div>
    @section('header')
    <div class="flex items-center justify-between gap-8">
        <div class="flex items-center gap-2">
            @include('livewire.common.header', ['icon' => 'question-mark-circle', 'title' =>
            __('support.help_support')])
        </div>
        <div class="w-1/2">
            @include('livewire.common.page-info', ['content' => __('support.page_info')])
        </div>
    </div>
    @endsection
    @if(!$messageSent)
    <div class="flex flex-col w-full md:w-2/3 xl:w-1/3 gap-4 m-auto">
        <div>
            <x-select label="Reason *" wire:model.defer="reason" placeholder="Select a reason" :options=$reasons />
        </div>
        <div>
            <x-select label="Tool" wire:model.defer="tool" placeholder="Select one or more" multiselect
                :options=$tools />
        </div>
        <div>
            <x-textarea label="Message *" wire:model.defer="message"
                placeholder="Provide as many information as possible" />
        </div>
        <div class="m-auto">
            <button wire:click="submit" type="button"
                class="bg-secondary text-white px-4 py-2 rounded-lg">Submit</button>
        </div>
    </div>
    @else
    <div class="flex flex-col gap-2 text-center h-full mt-12 text-gray-700">
        <div class="text-3xl font-bold">We just received your message!</div>
        <div class="text-xl">Our support team will be reaching out to your shortly</div>
    </div>
    @endif
</div>
