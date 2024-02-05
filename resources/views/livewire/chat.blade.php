<div>
    <div
        class="w-full md:w-1/3 fixed @if($isOpen) visible opacity-100 @else invisible opacity-0 @endif transition-all duration-500 ease-in-out bg-white top-0 right-0 z-50 h-screen shadow-lg flex flex-col">
        <div class="flex items-center justify-between p-6 bg-main">
            <div class="flex items-center gap-2 text-gray-800 text-2xl font-bold text-white">
                <x-icon name="speakerphone" width="28" height="28" />
                <div>Chat</div>
            </div>
            <div>
                <button wire:click="createThread"
                    class="flex items-center gap-1 w-full bg-gray-200 text-gray-700 rounded-lg px-2 py-1">
                    <x-icon name="plus" widht="32" height="32" />
                    <span class="font-bold">{{__('chat.new_chat')}}</span>
                </button>
            </div>
            <button wire:click="$toggle('isOpen')" class="text-white">
                <x-icon name="x-circle" width="42" height="42" />
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
                <div>{{__('chat.is_typing')}}</div>
                <div class="w-[20px]" id="typewriter">...</div>
            </div>
        </div>

        <div class="w-full flex flex-col justify-end p-6 bg-gray-100" x-data="{
            submitOnEnter: $wire.submitMsg,
            handleEnter(event) {
                if (!event.shiftKey) {
                    event.preventDefault();
                    this.submitOnEnter();
                }
            }
        }">
            @if($errors->has('inputMsg'))
            <span class="text-red-500 text-sm mb-2">{{ $errors->first('inputMsg') }}</span>
            @endif
            <textarea wire:model.live="inputMsg" x-on:keydown.enter="handleEnter($event)"
                placeholder="{{__('chat.type_message_here')}}" rows="4"
                class="w-full rounded-t-lg border-gray-200"></textarea>
            <button @if($processing) disabled @endif wire:click='submitMsg'
                class="bg-secondary text-white py-2 rounded-b-lg font-bold text-lg">{{__('chat.send')}}</button>
        </div>
    </div>

    @if(!$isOpen)
    <div class="fixed bottom-3 right-3 z-40">
        <button wire:click="$set('isOpen', true)"
            class="flex items-center gap-2 justify-center w-full bg-secondary m-0 p-3 rounded-full text-white flex items-center justify-center text-lg font-bold">
            <x-icon name="chat" width="40" height="40" />
        </button>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        initTypewriter('typewriter', ['...'], 120);
    });

    document.addEventListener("scrollToBottom", function() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    });
</script>
@endpush
