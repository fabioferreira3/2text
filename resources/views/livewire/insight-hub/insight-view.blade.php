<div class="flex flex-col gap-6 h-full overflow-auto">
    @section('header')
    @livewire('common.header', [ 'icon'=> 'search-circle',
    'title' => $document->title ?? __('insight-hub.new_inquiry'),
    'suffix' => $document->title ? __('insight-hub.insight_hub') : "",
    'document' => $document,
    'editable' => true
    ])
    @endsection

    <div class="flex gap-4 h-full">
        <div class="w-full md:w-2/5 h-full">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-3xl text-zinc-700">{{ __('insight-hub.sources') }}:</h2>
                    @include('livewire.common.help-item', [
                    'header' => __('insight-hub.sources'),
                    'content' => App\Helpers\InstructionsHelper::insightHubSources(30000)
                    ])
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button wire:click="createNewInsight()"
                        class="flex items-center gap-1 bg-main text-white font-bold px-2 py-1 rounded-lg">
                        <x-icon name="plus" width="18" height="18" />
                        <span class="text-xs">{{__('insight-hub.new')}}</span>
                    </button>
                </div>
            </div>
            <div class="mt-6">
                <div class="w-full flex flex-col gap-6">
                    <!-- Source -->
                    <div class="flex flex-col gap-3">
                        <select @if ($isProcessing) disabled @endif name="provider" wire:model.live="sourceType"
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
                        <input @if ($isProcessing) disabled @endif type="text" name="sourceUrl" wire:model.live="sourceUrl"
                            class="p-3 border border-zinc-200 rounded-lg w-full" />

                        @if ($errors->has('sourceUrl'))
                        <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                        @endif
                    </div>

                    @if($sourceType === App\Enums\SourceProvider::YOUTUBE->value)
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="text-xl font-bold text-gray-700">{{__('insight-hub.language')}}:</label>
                            @include('livewire.common.help-item', [
                            'header' => __('insight-hub.language'),
                            'content' => App\Helpers\InstructionsHelper::blogLanguages()
                            ])
                        </div>
                        <select name="videoLanguage" wire:model.live="videoLanguage"
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
                        <input @if ($isProcessing) disabled @endif type="file" name="fileInput" wire:model.live="fileInput"
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
                            {{__('insight-hub.text')}}:
                        </label>
                        <textarea @if ($isProcessing) disabled @endif class="border border-zinc-200 rounded-lg" rows="7"
                            maxlength="30000" wire:model.live="context"></textarea>
                        @if($errors->has('context'))
                        <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- END: Free Text -->

                    <!-- Generate button -->
                    @if(!$isProcessing)
                    <div class="flex mt-4">
                        <button wire:click="embed" wire:loading.remove wire:target="fileInput"
                            class="bg-secondary text-white rounded-lg py-2 px-4 font-bold text-xl">
                            {{__('insight-hub.submit')}}
                        </button>
                        <button disabled wire:loading wire:target="fileInput"
                            class="bg-secondary text-white rounded-lg py-2 px-4 font-bold text-xl">
                            {{__('insight-hub.please_wait')}}
                        </button>
                    </div>
                    @endif
                    <!-- END: Generate button -->

                    @if($isProcessing)
                    <!-- Loading -->
                    <div class="flex flex-col border-1 border rounded-lg bg-white p-8">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <x-loader height="10" width="10" />
                                <label class="font-bold text-zinc-700 text-2xl cursor-pointer">
                                    {{ __('insight-hub.embedding') }}<span id="typewriter"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- END: Loading -->
                    @endif
                </div>
            </div>
        </div>

        <div class="w-full md:w-3/5 border h-full rounded-lg p-4 bg-gray-100">
            <div class="flex flex-col justify-end gap-2 h-full">
                @if ($hasEmbeddings)
                @livewire('insight-hub.insight-chat', ['document' => $document])
                @endif

                @if(!$hasEmbeddings)
                <div class="flex items-center justify-center h-full">
                    {{__('insight-hub.no_inquiries')}}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>