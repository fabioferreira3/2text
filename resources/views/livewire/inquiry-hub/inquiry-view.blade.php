<div class="flex flex-col gap-6">
    @livewire('common.header', [ 'icon'=> 'search-circle',
    'title' => $document->title ?? __('inquiry-hub.new_inquiry'),
    'suffix' => $document->title ? __('inquiry-hub.inquiry_hub') : "",
    'document' => $document,
    'editable' => true
    ])

    <div class="flex gap-4">
        <div class="w-full md:w-2/5">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-3xl text-zinc-700">{{ __('inquiry-hub.sources') }}:</h2>
                    @include('livewire.common.help-item', [
                    'header' => __('inquiry-hub.sources'),
                    'content' => App\Helpers\InstructionsHelper::inquiryHubSources(30000)
                    ])
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button wire:click="createNewInquiry()"
                        class="flex items-center gap-1 bg-main text-white font-bold px-2 py-1 rounded-lg">
                        <x-icon name="plus" width="18" height="18" />
                        <span class="text-xs">{{__('inquiry-hub.new')}}</span>
                    </button>
                </div>
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
                    @if (in_array($sourceType, [App\Enums\SourceProvider::WEBSITE_URL->value,
                    App\Enums\SourceProvider::YOUTUBE->value]))

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

                    @if($sourceType === App\Enums\SourceProvider::YOUTUBE->value)
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="text-xl font-bold text-gray-700">{{__('inquiry-hub.language')}}:</label>
                            @include('livewire.common.help-item', [
                            'header' => __('inquiry-hub.language'),
                            'content' => App\Helpers\InstructionsHelper::blogLanguages()
                            ])
                        </div>
                        <select name="videoLanguage" wire:model="videoLanguage"
                            class="p-3 rounded-lg border border-zinc-200">
                            @include('livewire.common.languages-options')
                        </select>
                        @if($errors->has('videoLanguage'))
                        <span class="text-red-500 text-sm">{{ $errors->first('videoLanguage') }}</span>
                        @endif
                    </div>
                    @endif
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
                @if ($hasEmbeddings)
                @livewire('inquiry-hub.inquiry-chat', ['document' => $document])
                @endif

                @if(!$hasEmbeddings)
                <div class="flex items-center justify-center h-full">
                    {{__('inquiry-hub.no_inquiries')}}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
