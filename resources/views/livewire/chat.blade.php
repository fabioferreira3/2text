@if($isOpen)
<div class="fixed bg-white top-0 right-0 w-1/4 z-50 h-screen shadow-lg flex flex-col">
    <div class="flex items-center justify-between p-6 bg-main">
        <div class="text-gray-800 text-2xl font-bold text-white">Chat</div>
        <div>
            <button wire:click="createThread" class="flex items-center gap-1 w-full bg-gray-200 text-gray-700 rounded-lg px-2 py-1">
                <x-icon name="plus" widht="28" height="28" />
                <span class="font-bold">New chat</span>
            </button>
        </div>
        <button wire:click="$toggle('isOpen')" class="text-white">
            <x-icon name="x-circle" widht="42" height="42" />
        </button>
    </div>
    <div id="chatContainer" class="flex-grow flex flex-col overflow-y-auto px-6 pt-6">
        <div class="mt-auto space-y-6">
            @if (count($activeThread->iterations))
            @foreach ($activeThread->iterations as $iteration)
            @if($iteration->origin === 'user')
            @include('livewire.chat.user-msg', ['response' => $iteration->response])
            @else
            @include('livewire.chat.sys-msg', ['response' => $iteration->response])
            @endif
            @endforeach
            @endif
        </div>
        <div class="my-4 flex items-center justify-end italic @if(!$processing) invisible @endif">
            <div>system is typing</div>
            <div class="w-[20px]" id="typewriter">...</div>
        </div>
    </div>

    <div class="w-full flex flex-col justify-end p-6 bg-gray-100">
        @if($errors->has('inputMsg'))
        <span class="text-red-500 text-sm mb-2">{{ $errors->first('inputMsg') }}</span>
        @endif
        <textarea wire:model="inputMsg" placeholder="Type your message here..." rows="4" class="w-full rounded-t-lg border-gray-200"></textarea>
        <button :disabled="$processing" wire:click='submitMsg' class="bg-secondary text-white py-2 rounded-b-lg font-bold text-lg">Send</button>
    </div>
    @endif
    @if(!$isOpen)
    <div class="fixed top-1/3 right-0 z-40 w-[30px]">
        <button wire:click="$set('isOpen', true)" class="flex flex-col items-center justify-center w-full bg-secondary m-0 px-4 py-2 rounded-l-xl text-white flex items-center justify-center text-xl font-bold">
            <div>H</div>
            <div>E</div>
            <div>L</div>
            <div>P</div>
        </button>
    </div>
</div>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        initTypewriter('typewriter', ['...'], 120);
    });

    document.addEventListener("scrollToBottom", function() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    });
</script>