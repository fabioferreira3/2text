<div class="flex flex-col gap-6 md:mb-24 md:mb-0">
    @section('header')
    @include('livewire.common.header', ['icon' => 'sort-ascending', 'title' => __('summarizer.new_summary')])
    @endsection
    @if(!$isProcessing)
    <div class="flex flex-col gap-6 p-4 border rounded-lg">
        <div class="w-full flex flex-col md:grid md:grid-cols-3 gap-6">
            <!-- Source -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.source')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.source'),
                    'content' => App\Helpers\InstructionsHelper::sources()
                    ])
                </div>
                <select name="provider" wire:model.live="source" class="p-3 rounded-lg border border-zinc-200">
                    @foreach([App\Enums\SourceProvider::FREE_TEXT, App\Enums\SourceProvider::YOUTUBE] as $provider)
                    <option wire:key="{{$provider->value}}" value="{{$provider->value}}">{{$provider->label()}}</option>
                    @endforeach
                </select>
            </div>
            <!-- END: Source -->

            <!-- Source Language -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.source_language')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.source_language'),
                    'content' => App\Helpers\InstructionsHelper::summarizerLanguages()
                    ])
                </div>
                <select name="language" wire:model.live="sourceLanguage" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.languages-options')
                </select>
                @if($errors->has('sourceLanguage'))
                <span class="text-red-500 text-sm">{{ $errors->first('sourceLanguage') }}</span>
                @endif
            </div>
            <!-- END: Source Language -->

            <!-- Target Language -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.target_language')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.target_language'),
                    'content' => App\Helpers\InstructionsHelper::summarizerLanguages()
                    ])
                </div>
                <select name="language" wire:model.live="targetLanguage" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.languages-options')
                </select>
                @if($errors->has('targetLanguage'))
                <span class="text-red-500 text-sm">{{ $errors->first('targetLanguage') }}</span>
                @endif
            </div>
            <!-- END: Target Language -->
        </div>
        <div class="w-full flex flex-col md:grid md:grid-cols-3 md:items-center gap-6">
            <!-- File input -->
            {{-- @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700">{{ __('blog.file_option') }}</label>
                <input type="file" name="fileInput" wire:model.live="fileInput"
                    class="p-3 border border-zinc-200 rounded-lg w-full" />
                @if ($errors->has('fileInput'))
                <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
                @endif
            </div>
            @endif --}}
            <!-- END: File input -->

            <!-- Free Text -->
            @if ($source === 'free_text')
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700 flex items-center">
                    Text:
                </label>
                <textarea class="border border-zinc-200 rounded-lg" rows="5" maxlength="30000"
                    wire:model.live="context"></textarea>
                @if($errors->has('context'))
                <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                @endif
            </div>
            @endif
            <!-- END: Free Text -->

            <!-- Source URLs -->
            @if ($source === 'website_url' || $source === 'youtube')
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700 flex items-center">
                    URL
                </label>
                <input type="text" name="sourceUrl" wire:model.live="sourceUrl"
                    class="p-3 border border-zinc-200 rounded-lg w-full" />

                @if ($errors->has('sourceUrl'))
                <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                @endif
            </div>
            @endif
            <!-- END: Source URLs -->

            <!-- Word count -->
            <div class="flex flex-col gap-3 col-span-1">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.word_count')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.word_count'),
                    'content' => App\Helpers\InstructionsHelper::wordsCount()
                    ])
                </div>
                <input type="number" min="50" max="600" name="max_words_count" wire:model.blur="maxWordsCount"
                    class="p-3 rounded-lg border border-zinc-200 w-2/3" />
                @if($errors->has('maxWordsCount'))
                <span class="text-red-500 text-sm">{{ $errors->first('maxWordsCount') }}</span>
                @endif
            </div>
            <!-- END: Word count -->
        </div>

        <!-- Generate button -->
        <div class="flex justify-center mt-4">
            <button wire:click="process" wire:loading.remove
                class="bg-secondary text-xl text-white font-bold px-4 py-2 rounded-lg">
                {{__('summarizer.start')}}
            </button>
        </div>
        <!-- END: Generate button -->
    </div>
    @endif

    <div class="{{ $isProcessing ? 'flex' : 'hidden' }} flex flex-col border-1 border rounded-lg bg-white p-8">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <x-loader height="10" width="10" />
                <label class="font-bold text-zinc-700 text-2xl cursor-pointer">
                    {{ __('summarizer.generating') }}<span id="typewriter"></span>
                </label>
            </div>
        </div>
    </div>
</div>