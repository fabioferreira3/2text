<div class="flex flex-col justify-end gap-2 h-full">
    <div id="inquiryChatContainer" class="flex-grow overflow-y-auto bg-white rounded-lg border border-gray-200">
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
    <!-- Typing Indicator -->
    <div class="my-2 flex items-center justify-end italic @if(!$isProcessing) invisible @endif">
        <div>{{__('chat.is_typing')}}</div>
        <div class="w-[20px]" id="typewriter">...</div>
    </div>

    <!-- Message Input -->
    <div class="w-full flex flex-col justify-end" x-data="{
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
            placeholder="{{__('chat.type_message_here')}}" rows="2"
            class="w-full rounded-t-lg border-gray-200"></textarea>
        <button @if($isProcessing) disabled @endif wire:click='submitMsg'
            class="bg-secondary text-white py-1 rounded-b-lg font-bold text-lg">
            {{__('insight-hub.send')}}
        </button>
    </div>
</div>

@push('scripts')
<script>
    function scrollBottom() {
        const container = document.getElementById('inquiryChatContainer');
        container.scrollTop = container.scrollHeight;
    }
    document.addEventListener("scrollInquiryChatToBottom", scrollBottom);

    scrollBottom();
</script>
@endpush