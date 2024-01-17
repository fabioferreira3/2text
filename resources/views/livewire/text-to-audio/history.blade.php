<div>
    @if($displayHeader)
    @include('livewire.common.header', ['icon' => 'volume-up', 'title' => __('text-to-audio.audio_history')])
    @endif
    @include('livewire.text-to-audio.history-table', ['history' => $history])
    @if ($selectedMediaFile)
    <x-experior::modal>
        @include('livewire.common.header', ['icon' => 'trash', 'title' =>
        __('text-to-audio.delete_confirmation_header')])
        <div class="mt-4 text-lg">{{__('text-to-audio.delete_confirmation')}}</div>
        <div class="flex items-center justify-center gap-2 mt-6">
            <button wire:click="abortDeletion"
                class="bg-gray-100 text-lg px-4 py-2 font-bold rounded-lg text-gray-600">{{__('text-to-audio.cancel')}}</button>
            <button wire:click="delete"
                class="bg-secondary text-lg px-4 py-2 font-bold rounded-lg text-white">{{__('text-to-audio.confirm')}}</button>
        </div>
    </x-experior::modal>
    @endif
</div>