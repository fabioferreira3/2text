<div class="flex flex-col gap-6">
    @livewire('common.header', [ 'icon'=> 'search-circle',
    'title' => $document->title ?? __('inquiry-hub.new_inquiry'),
    'suffix' => $document->title ? __('inquiry-hub.inquiry_hub') : "",
    'document' => $document,
    'editable' => true
    ])
    <div class="flex items-center justify-end gap-2">
        <button wire:click="createNewInquiry()"
            class="flex items-center gap-1 bg-main text-white font-bold px-5 py-1 rounded-lg">
            <x-icon name="plus" width="24" height="24" />
            <span class="text-lg">{{__('inquiry-hub.new')}}</span>
        </button>
    </div>

    <div class="flex gap-4">
        <div class="w-full md:w-2/5">
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
                        <select @if ($isProcessing) disabled @endif name="provider" wire:model="sourceType"
                            class="p-3 rounded-lg border border-zinc-200">
                            @include('livewire.common.source-providers-options')
                        </select>
                        @if ($errors->has('sourceType'))
                        <span class="text-red-500 text-sm">{{ $errors->first('sourceType') }}</span>
                        @endif
                    </div>
                    <!-- END: Source -->

                    <!-- Source URLs -->
                    @if ($sourceType === 'website_url' || $sourceType === 'youtube')
                    <div class="flex flex-col gap-3">
                        <label class="font-bold text-xl text-zinc-700 flex items-center">
                            URL
                        </label>
                        <input @if ($isProcessing) disabled @endif type="text" name="sourceUrl" wire:model="sourceUrl"
                            class="p-3 border border-zinc-200 rounded-lg w-full" />

                        @if ($errors->has('sourceUrl'))
                        <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: Source URLs -->

                    <!-- File input -->
                    @if (in_array($sourceType, ['docx', 'pdf_file', 'csv', 'json']))
                    <div class="flex flex-col gap-3 col-span-2">
                        <label class="font-bold text-xl text-zinc-700">{{ __('blog.file_option') }}</label>
                        <input @if ($isProcessing) disabled @endif type="file" name="fileInput" wire:model="fileInput"
                            class="p-3 border border-zinc-200 rounded-lg w-full" />
                        @if ($errors->has('fileInput'))
                        <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: File input -->

                    <!-- Free Text -->
                    @if ($sourceType === 'free_text')
                    <div class="flex flex-col gap-3 col-span-2">
                        <label class="font-bold text-xl text-zinc-700 flex items-center">
                            {{__('inquiry-hub.text')}}:
                        </label>
                        <textarea @if ($isProcessing) disabled @endif class="border border-zinc-200 rounded-lg" rows="7"
                            maxlength="30000" wire:model="context"></textarea>
                        @if($errors->has('context'))
                        <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: Free Text -->

                    <!-- Generate button -->
                    @if(!$isProcessing)
                    <div class="flex mt-4">
                        <button wire:click="embed" wire:loading.remove
                            class="bg-secondary text-white rounded-lg py-2 px-4 font-bold text-xl">
                            {{__('inquiry-hub.submit')}}
                        </button>
                    </div>
                    @endif
                    <!-- END: Generate button -->

                    <!-- Loadinng -->
                    <div
                        class="{{ $isProcessing ? 'flex' : 'hidden' }} flex flex-col border-1 border rounded-lg bg-white p-8">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <x-loader height="10" width="10" />
                                <label class="font-bold text-zinc-700 text-2xl cursor-pointer">
                                    {{ __('inquiry-hub.embedding') }}<span id="typewriter"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- END: Loading -->
                </div>
            </div>
        </div>

        <div class="w-full md:w-3/5 border max-h-[32rem] rounded-lg p-4 bg-gray-100">
            <div class="flex flex-col justify-end gap-2 h-full">
                <div id="inquiryChatContainer"
                    class="flex-grow overflow-y-auto bg-white rounded-lg border border-gray-200">

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
                            <div
                                class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden flex justify-center items-center">
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
                    </div>
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