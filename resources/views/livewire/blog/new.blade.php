<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'newspaper', 'label' => __('blog.new_blog_post')])
    <div class="flex flex-col">
        <div class="flex flex-col gap-6 p-4 border rounded-lg">
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.source')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.source'),
                        'content' => App\Helpers\InstructionsHelper::sources()
                        ])
                    </div>
                    <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                        @include('livewire.common.source-providers-options')
                    </select>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.keyword')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.keyword'),
                        'content' => App\Helpers\InstructionsHelper::blogKeyword()
                        ])
                    </div>
                    <input name="keyword" wire:model="keyword" class="p-3 rounded-lg border border-zinc-200" />
                    @if($errors->has('keyword'))
                    <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                    @endif
                </div>
            </div>

            @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
            <label class="font-bold text-lg text-zinc-700">{{ __('social_media.file_option') }}</label>
            <input type="file" name="fileInput" wire:model="fileInput"
                class="p-3 border border-zinc-200 rounded-lg w-full" />
            @endif
            @if ($errors->has('fileInput'))
            <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
            @endif

            @if ($source === 'website_url' || $source === 'youtube')
            <label class="font-bold text-lg text-zinc-700 flex items-center justify-between">
                @if($source === 'youtube') {{ __('social_media.youtube_option') }} <span
                    class="text-sm">{{__('social_media.max_permitted_youtube_links', ['max'
                    => 3])}}</span>
                @else {{ __('social_media.url_option') }} <span class="text-sm">{{__('social_media.max_permitted_urls',
                    ['max'
                    => 5])}}</span>
                @endif
            </label>
            @if(count($sourceUrls))
            <div class="flex flex-col gap-1 my-2">
                @foreach ($sourceUrls as $sourceUrl)
                <div class="flex items-center gap-2">
                    <div class="bg-gray-100 px-3 py-1 rounded-lg">{{$sourceUrl}}</div>
                    <button class="outline-none focus:outline-none" wire:click="removeSourceUrl('{{$sourceUrl}}')">
                        <x-icon name="x-circle" width="24" height="24" class="text-gray-600" />
                    </button>
                </div>
                @endforeach
            </div>
            @endif

            @if(!$maxSourceUrlsReached)
            <div class="flex items-center gap-2" x-data="{
                                            submitOnEnter: $wire.addSourceUrl,
                                            handleEnter(event) {
                                                if (!event.shiftKey) {
                                                    event.preventDefault();
                                                    this.submitOnEnter();
                                                }
                                            }
                                        }">
                <input name="url" x-on:keydown.enter="handleEnter($event)" wire:model.defer="tempSourceUrl"
                    class="p-3 border border-zinc-200 rounded-lg w-full" />
                <button wire:click="addSourceUrl()" class="bg-secondary text-white p-1 rounded-full">
                    <x-icon name="plus" width="24" height="24" />
                </button>
            </div>
            @endif
            @if ($errors->has('tempSourceUrl'))
            <span class="text-red-500 text-sm">{{ $errors->first('tempSourceUrl') }}</span>
            @endif
            @endif
            @if ($errors->has('sourceUrls'))
            <span class="text-red-500 text-sm">{{ $errors->first('sourceUrls') }}</span>
            @endif

            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1">
                    <label>{{__('blog.context')}}:</label>
                    <small>{{__('blog.briefly_describe', ['minWords' => '30'])}}</small>
                    <small>{{__('blog.paste_content', ['maxChars' => '30000'])}}</small>
                </div>
                <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="30000"
                    wire:model="context"></textarea>
                @if($errors->has('context'))
                <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                @endif
            </div>

            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.topics_number')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.topics_number'),
                        'content' => App\Helpers\InstructionsHelper::maxSubtopics()
                        ])
                    </div>
                    <input type="number" max="10" name="target_headers_count" wire:model="targetHeadersCount"
                        class="p-3 rounded-lg border border-zinc-200" />
                    @if($errors->has('targetHeadersCount'))
                    <span class="text-red-500 text-sm">{{ $errors->first('targetHeadersCount') }}</span>
                    @endif
                </div>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.language')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.language'),
                        'content' => App\Helpers\InstructionsHelper::blogLanguages()
                        ])
                    </div>
                    <select name="language" wire:model="language" class="p-3 rounded-lg border border-zinc-200">
                        @include('livewire.common.languages-options')
                    </select>
                    @if($errors->has('language'))
                    <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                    @endif
                </div>
            </div>
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.writing_style')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.writing_style'),
                        'content' => App\Helpers\InstructionsHelper::writingStyles()
                        ])
                    </div>
                    <select name="style" wire:model="style" class="p-3 rounded-lg border border-zinc-200">
                        @include('livewire.common.styles-options')
                    </select>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.tone')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.tone'),
                        'content' => App\Helpers\InstructionsHelper::writingTones()
                        ])
                    </div>
                    <select name="tone" wire:model="tone" class="p-3 rounded-lg border border-zinc-200">
                        @include('livewire.common.tones-options')
                    </select>
                </div>
            </div>
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>Generate Hero Image:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.writing_style'),
                        'content' => App\Helpers\InstructionsHelper::writingStyles()
                        ])
                    </div>
                    <div class="md:col-span-1">
                        <x-checkbox md id="generate_img" name="generate_img" label="Yes" wire:model="generateImage" />
                    </div>
                </div>
                @if($generateImage)
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('blog.hero_image_description')}}:</label>
                        @include('livewire.common.help-item', [
                        'header' => __('blog.writing_style'),
                        'content' => App\Helpers\InstructionsHelper::writingStyles()
                        ])
                    </div>
                    <div class="w-full">
                        <textarea placeholder="{{__('blog.placeholder_example')}}"
                            class="border border-zinc-200 rounded-lg w-full" rows="3" maxlength="2000"
                            wire:model="imgPrompt"></textarea>
                    </div>
                </div>
                @endif
            </div>
            <div class="flex justify-center mt-4">
                <button wire:click="process" wire:loading.remove
                    class="bg-secondary text-white font-bold px-4 py-2 rounded-lg">
                    {{__('blog.generate')}}!
                </button>
            </div>
        </div>
    </div>
</div>