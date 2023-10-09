<div>@include('livewire.common.header', ['icon' => 'volume-up', 'label' => __('text-to-speech.audio_history')])
    <div class="w-full mt-8">
        @if(count($history))
        <table class="w-full table-auto">
            <thead>
                <tr>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">Date</div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">Content</div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">Voice</div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">Actions</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $document)
                <tr>
                    <td class="h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg">{{$document['created_at']}}</div>
                    </td>
                    <td class="h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg italic">"{{$document['content']}}"</div>
                    </td>
                    <td class="h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg">{{$document['voice']['name']}}</div>
                    </td>
                    <td class="h-12 align-top">
                        <div class="h-full px-4 py-2 flex items-center gap-4 text-gray-500 border rounded-lg">
                            <button wire:click="playAudio('{{ $document['media_file']['id'] }}')">
                                <x-icon name="play" width="26" height="26" />
                            </button>
                            <button wire:click="download('{{$document['media_file']['id']}}')">
                                <x-icon name="download" width="26" height="26" />
                            </button>
                            <button wire:click="displayDeleteModal('{{$document['media_file']['id']}}')">
                                <x-icon name="trash" width="26" height="26" />
                            </button>
                            <audio id="{{ $document['media_file']['id'] }}" src="{{ $document['media_file']['url'] }}" preload="auto"></audio>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if ($selectedMediaFile)
    <x-experior::modal>
        @include('livewire.common.header', ['icon' => 'trash', 'label' => __('text-to-speech.delete_confirmation_header')])
        <div class="mt-4 text-lg">{{__('text-to-speech.delete_confirmation')}}</div>
        <div class="flex items-center gap-2 mt-6">
            <button wire:click="delete" class="bg-secondary text-lg px-4 py-2 font-bold rounded-lg text-white">Confirm</button>
            <button wire:click="abortDeletion" class="bg-gray-100 text-lg px-4 py-2 font-bold rounded-lg text-gray-600">Cancel</button>
        </div>
    </x-experior::modal>
    @endif
</div>