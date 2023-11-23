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
        {{--
        <!-- User Message -->
        <div class="flex items-end justify-end p-4">
            <div class="max-w-full lg:max-w-3/4 text-right">
                <div class="inline-block bg-gray-100 text-gray-700 rounded-t-xl rounded-bl-xl p-3">
                    Batman tornou-se popular assim que foi apresentado, acabando por ganhar a sua
                    própria revista de banda desenhada em
                    1940, Batman. Enquanto as décadas progrediram, foram surgindo divergências sobre a
                    interpretação do personagem....
                </div>
            </div>
            <div class="ml-3 flex-shrink-0">
                <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden flex justify-center items-center">
                    <!-- Centering the icon -->
                    <x-icon name="user" width="24" height="24" class="text-gray-600" />
                    <!-- Smaller icon size -->
                </div>
            </div>
        </div>

        <!-- System Message -->
        <div class="flex items-end justify-start p-4">
            <div class="mr-3 flex-shrink-0">
                <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                    <img src="/oraculum.webp" class="object-cover w-full h-full" />
                </div>
            </div>
            <div class="max-w-full lg:max-w-3/4">
                <div class="inline-block bg-gray-300 rounded-t-xl rounded-br-xl p-3">
                    Good morning!
                </div>
            </div>
        </div> --}}
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
        <textarea wire:model="inputMsg" x-on:keydown.enter="handleEnter($event)"
            placeholder="{{__('chat.type_message_here')}}" rows="2"
            class="w-full rounded-t-lg border-gray-200"></textarea>
        <button @if($isProcessing) disabled @endif wire:click='submitMsg'
            class="bg-secondary text-white py-1 rounded-b-lg font-bold text-lg">
            {{__('inquiry-hub.send')}}
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
