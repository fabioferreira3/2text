<div>
    @if($displayHeader)
    @include('livewire.common.header', ['icon' => 'volume-up', 'label' => __('text-to-speech.audio_history')])
    @endif
    @include('livewire.text-to-speech.history-table', ['history' => $history])
    @if ($selectedMediaFile)
    <x-experior::modal>
        @include('livewire.common.header', ['icon' => 'trash', 'label' =>
        __('text-to-speech.delete_confirmation_header')])
        <div class="mt-4 text-lg">{{__('text-to-speech.delete_confirmation')}}</div>
        <div class="flex items-center gap-2 mt-6">
            <button wire:click="delete"
                class="bg-secondary text-lg px-4 py-2 font-bold rounded-lg text-white">{{__('text-to-speech.confirm')}}</button>
            <button wire:click="abortDeletion"
                class="bg-gray-100 text-lg px-4 py-2 font-bold rounded-lg text-gray-600">{{__('text-to-speech.cancel')}}</button>
        </div>
    </x-experior::modal>
    @endif
</div>
