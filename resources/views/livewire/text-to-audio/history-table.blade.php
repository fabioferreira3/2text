<div class="w-full mt-8">
    @if(count($history))
    <table class="w-full table-auto">
        <thead>
            <tr>
                <th>
                    <div class="bg-gray-200 text-left px-4 py-2 border rounded-lg">{{__('text-to-audio.date')}}
                    </div>
                </th>
                <th>
                    <div class="bg-gray-200 text-left px-4 py-2 border rounded-lg">{{__('text-to-audio.voice')}}
                    </div>
                </th>
                <th>
                    <div class="bg-gray-200 text-left px-4 py-2 border rounded-lg">{{__('text-to-audio.content')}}
                    </div>
                </th>
                <th>
                    <div class="bg-gray-200 text-left px-4 py-2 border rounded-lg">{{__('text-to-audio.actions')}}
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $document)
            <tr>
                <td class="h-12 align-top">
                    <div class="flex items-center h-full px-4 py-2 border rounded-lg text-xs bg-white">
                        {{$document['created_at']}}
                    </div>
                </td>
                <td class="h-12 align-top">
                    <div class="flex items-center h-full px-4 py-2 border rounded-lg text-sm bg-white">
                        {{$document['voice']['name']}}
                    </div>
                </td>
                <td class="relative group/content h-12 align-top">
                    <div class="flex items-center h-full px-4 py-2 border rounded-lg italic bg-white">
                        "{{$document['short_content']}}"</div>
                    <div
                        class="invisible group-hover/content:visible opacity-0 group-hover/content:opacity-100 absolute transition-opacity duration-200 ease-in-out px-4 py-2 bg-gray-100 w-full z-20 border border-gray-400 rounded-lg italic">
                        {{$document['content']}}</div>
                </td>
                <td class="h-12 align-top">
                    <div class="h-full px-4 py-2 flex items-center gap-4 text-gray-500 border rounded-lg bg-white">
                        <button class="relative group/content"
                            wire:click="playAudio('{{ $document['media_file']['id'] }}')">
                            <x-icon name="play" width="26" height="26" />
                            <div
                                class="absolute invisible group-hover/content:visible opacity-0 group-hover/content:opacity-100 absolute transition-opacity duration-200 ease-in-out px-2 py-1 bg-gray-800 text-gray-100 z-20 rounded-lg text-xs">
                                {{__('text-to-audio.play')}}</div>
                        </button>
                        <button class="relative group/content"
                            wire:click="download('{{$document['media_file']['id']}}')">
                            <x-icon name="download" width="26" height="26" />
                            <div
                                class="absolute invisible group-hover/content:visible opacity-0 group-hover/content:opacity-100 absolute transition-opacity duration-200 ease-in-out px-2 py-1 bg-gray-800 text-gray-100 z-20 rounded-lg text-xs">
                                {{__('text-to-audio.download')}}</div>
                        </button>
                        <button class="relative group/content"
                            wire:click="displayDeleteModal('{{$document['media_file']['id']}}')">
                            <x-icon name="trash" width="26" height="26" />
                            <div
                                class="absolute invisible group-hover/content:visible opacity-0 group-hover/content:opacity-100 absolute transition-opacity duration-200 ease-in-out px-2 py-1 bg-gray-800 text-gray-100 z-20 rounded-lg text-xs">
                                {{__('text-to-audio.delete')}}</div>
                        </button>
                        <audio id="{{ $document['media_file']['id'] }}" src="{{ $document['media_file']['url'] }}"
                            preload="auto"></audio>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="w-full h-full flex items-center justify-center mt-24">
        <div class="bg-white rounded-lg px-8 py-4">
            <div class="text-2xl font-bold text-gray-700">{{__('audio.no_audio')}}</div>
        </div>
    </div>
    @endif
</div>
