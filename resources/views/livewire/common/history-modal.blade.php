<div id="modal" class="fixed inset-0 w-full h-full z-20 bg-black bg-opacity-30 flex items-center justify-center backdrop-blur-sm">
    <div class="relative overflow-auto max-h-[75%] bg-white rounded-lg shadow-lg w-full sm:w-4/5 md:w-3/5 lg:w-3/5">
        <div class="py-4 text-left px-6">
            <div role='button' class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold">Log - {{$fieldTitle}}</p>
                <div role="button" class="cursor-pointer z-50" id="close" wire:click="$emitUp('closeHistoryModal')">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                        <path d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z"></path>
                    </svg>
                </div>
            </div>

            @if ($history->count())
                <div class="flex flex-col gap-2">
                    @foreach($history as $key => $item)
                    <div class="flex flex-col gap-3 rounded-lg border border-zinc-200 p-2">
                        <div class="flex items-center justify-between">
                            <div class='font-bold'>{{$item['created_at']}}</div>
                            @if ($key === 0) <span>Current</span>@endif
                            @if ($key > 0 )<x-button sm class='rounded-lg text-white bg-secondary hover:bg-black' wire:click="apply('{{ base64_encode($item['content']) }}')" label="Apply"/>@endif
                        </div>
                        <div class='italic text-sm'>
                            {!!Str::limit($item['content'], 300, ' (...)')!!}
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
            No history available
            @endif
            <div class="flex justify-center pt-4 z-30">
                <x-button label='Close' slate wire:click="$emitUp('closeHistoryModal')" class="py-2 px-4 rounded-lg" type="button"/>
            </div>
        </div>
    </div>

</div>
