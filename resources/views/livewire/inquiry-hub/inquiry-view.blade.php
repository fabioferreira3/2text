<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between border-b border-zinc-100">
        <div class="w-full">
            @livewire('common.header', [
            'icon' => 'search-circle',
            'title' => $document->title ?? __('inquiry-hub.new_inquiry'),
            'suffix' => $document->title ? __('inquiry-hub.inquiry_hub') : "",
            'document' => $document,
            'editable' => true
            ])
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="createNewInquiry()"
                class="flex items-center gap-1 bg-main text-white font-bold px-3 py-1 rounded-lg">
                <x-icon name="plus" width="20" height="20" />
                <span>{{__('inquiry-hub.new')}}</span>
            </button>
        </div>
    </div>
    <div class="flex gap-4">
        <div class="w-full md:w-1/2">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-3xl text-zinc-700">{{ __('inquiry-hub.sources') }}:</h2>
                @include('livewire.common.help-item', [
                'header' => __('blog.source'),
                'content' => App\Helpers\InstructionsHelper::sources()
                ])
            </div>
            <div class="mt-6">
                <div class="w-full flex flex-col gap-6">
                    <!-- Source -->
                    <div class="flex flex-col gap-3">
                        <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                            @include('livewire.common.source-providers-options')
                        </select>
                    </div>
                    <!-- END: Source -->

                    <!-- Source URLs -->
                    @if ($source === 'website_url' || $source === 'youtube')
                    <div class="flex flex-col gap-3">
                        <label class="font-bold text-xl text-zinc-700 flex items-center">
                            URL
                        </label>
                        <input type="text" name="sourceUrl" wire:model="sourceUrl"
                            class="p-3 border border-zinc-200 rounded-lg w-full" />

                        @if ($errors->has('sourceUrl'))
                        <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: Source URLs -->

                    <!-- File input -->
                    @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
                    <div class="flex flex-col gap-3 col-span-2">
                        <label class="font-bold text-xl text-zinc-700">{{ __('blog.file_option') }}</label>
                        <input type="file" name="fileInput" wire:model="fileInput"
                            class="p-3 border border-zinc-200 rounded-lg w-full" />
                        @if ($errors->has('fileInput'))
                        <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: File input -->

                    <!-- Free Text -->
                    @if ($source === 'free_text')
                    <div class="flex flex-col gap-3 col-span-2">
                        <label class="font-bold text-xl text-zinc-700 flex items-center">
                            {{__('inquiry-hub.text')}}:
                        </label>
                        <textarea class="border border-zinc-200 rounded-lg" rows="5" maxlength="30000"
                            wire:model="context"></textarea>
                        @if($errors->has('context'))
                        <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: Free Text -->

                    <div class="w-full flex justify-center">
                        <button
                            class="bg-secondary text-white rounded-lg py-2 px-4 font-bold text-xl">{{__('inquiry-hub.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/2 border max-h-[32rem] rounded-lg p-4 bg-gray-100">
            <div class="flex flex-col justify-end gap-2 h-full">
                <div id="inquiryChatContainer"
                    class="flex-grow flex flex-col overflow-y-auto bg-white rounded-lg border-gray-200 border pt-6">
                    <div class="mt-auto space-y-6 px-6">
                        @include('livewire.chat.sys-msg', ['response' => 'yes'])
                        @include('livewire.chat.user-msg', ['response' => 'eita'])
                        @include('livewire.chat.user-msg', ['response' => 'eita'])
                        @include('livewire.chat.user-msg', ['response' => 'eita'])
                        @include('livewire.chat.sys-msg', ['response' => 'O Batman (inicialmente chamado o Bat-Man)
                        também conhecido pelas alcunhas Homem-Morcego, Cavaleiro das Trevas, Cruzado
                        Encapuzado, Maior Detetive do Mundo,[1] é um personagem fictício e super-herói encapuzado da
                        editora norte-americana DC
                        Comics'])
                        @include('livewire.chat.user-msg', ['response' => 'O Batman (inicialmente chamado o Bat-Man)
                        também conhecido pelas alcunhas Homem-Morcego, Cavaleiro das Trevas, Cruzado
                        Encapuzado, Maior Detetive do Mundo,[1] é um personagem fictício e super-herói encapuzado da
                        editora norte-americana DC
                        Comics.'])
                    </div>
                    <div class="my-4 flex items-center justify-end italic @if($isProcessing) invisible @endif">
                        <div>{{__('chat.is_typing')}}</div>
                        <div class="w-[20px]" id="typewriter">...</div>
                    </div>
                </div>

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
                        class="bg-secondary text-white py-1 rounded-b-lg font-bold text-lg">{{__('inquiry-hub.send')}}</button>
                </div>
            </div>
        </div>
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
