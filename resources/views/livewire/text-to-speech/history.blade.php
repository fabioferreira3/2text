<div>@include('livewire.common.header', ['icon' => 'volume-up', 'label' => __('text-to-speech.audio_history')])
    <div class="w-full mt-8">
        @if(count($history))
        <table class="w-full table-auto">
            <thead>
                <tr>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">{{__('text-to-speech.date')}}
                        </div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">{{__('text-to-speech.content')}}
                        </div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">{{__('text-to-speech.voice')}}
                        </div>
                    </th>
                    <th>
                        <div class="bg-gray-100 text-left px-4 py-2 border rounded-lg">{{__('text-to-speech.actions')}}
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $document)
                <tr>
                    <td class="h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg text-xs">
                            {{$document['created_at']}}
                        </div>
                    </td>
                    <td class="relative group/content h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg italic">
                            "{{$document['short_content']}}"</div>
                        <div
                            class="invisible group-hover/content:visible opacity-0 group-hover/content:opacity-100 absolute transition-opacity duration-200 ease-in-out px-4 py-2 bg-gray-100 w-full z-20 border border-gray-400 rounded-lg italic">
                            {{$document['content']}}</div>
                    </td>
                    <td class="h-12 align-top">
                        <div class="flex items-center h-full px-4 py-2 border rounded-lg text-sm">
                            {{$document['voice']['name']}}
                        </div>
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
                            <audio id="{{ $document['media_file']['id'] }}" src="{{ $document['media_file']['url'] }}"
                                preload="auto"></audio>
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
