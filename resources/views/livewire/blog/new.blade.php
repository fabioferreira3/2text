<div class="flex flex-col gap-6 mb-24 md:mb-0">
    @section('header')
    <div class="flex flex-col md:flex-row items-center justify-between gap-2 md:gap-8">
        @include('livewire.common.header', ['icon' => 'newspaper', 'title' => __('blog.new_blog_post')])
        <div class="bg-gray-200 px-3 py-1 rounded-lg text-gray-700 text-lg md:text-sm font-semibold">
            1 {{__('common.unit')}} = 480 {{__('common.words')}}
        </div>
    </div>
    @endsection
    <div class="flex flex-col gap-6 p-4 border rounded-lg">

        <!-- Source, number of topics and language -->
        <div class="w-full flex flex-col md:grid md:grid-cols-3 gap-6">
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.source')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.source'),
                    'content' => App\Helpers\InstructionsHelper::sources()
                    ])
                </div>
                <select name="provider" wire:model.live="source" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.source-providers-options')
                </select>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.topics_number')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.topics_number'),
                    'content' => App\Helpers\InstructionsHelper::maxSubtopics()
                    ])
                </div>
                <input type="number" min="2" max="10" name="target_headers_count" wire:model.live="targetHeadersCount"
                    class="p-3 rounded-lg border border-zinc-200" />
                @if($errors->has('targetHeadersCount'))
                <span class="text-red-500 text-sm">{{ $errors->first('targetHeadersCount') }}</span>
                @endif
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.language')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.language'),
                    'content' => App\Helpers\InstructionsHelper::blogLanguages()
                    ])
                </div>
                <select name="language" wire:model.live="language" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.languages-options')
                </select>
                @if($errors->has('language'))
                <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                @endif
            </div>
        </div>
        <!-- END: Source, number of topics and language -->

        <!-- File input -->
        @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
        <div>
            <label class="font-bold text-xl text-zinc-700">{{ __('blog.file_option') }}</label>
            <input type="file" name="fileInput" wire:model.live="fileInput"
                class="p-3 border border-zinc-200 rounded-lg w-full" />
            @if ($errors->has('fileInput'))
            <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
            @endif
        </div>
        @endif
        <!-- END: File input -->

        <!-- Source URLs -->
        @if ($source === 'website_url' || $source === 'youtube')
        <div class="w-1/2">
            <label class="font-bold text-xl text-zinc-700 flex items-center gap-3">
                @if($source === 'youtube') {{ __('blog.youtube_option') }} <span
                    class="text-base">{{__('blog.max_permitted_youtube_links', ['max'
                    => 3])}}</span>
                @else {{ __('blog.url_option') }} <span class="text-base">{{__('blog.max_permitted_urls',
                    ['max'
                    => 5])}}</span>
                @endif
            </label>
            @if(count($sourceUrls))
            <div class="flex flex-col gap-1 my-2">
                @foreach ($sourceUrls as $sourceUrl)
                <div wire:key="{{$sourceUrl}}" class="flex items-center gap-2">
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
                <input name="url" placeholder="Paste a url here" x-on:keydown.enter="handleEnter($event)"
                    wire:model="tempSourceUrl" class="p-3 border border-zinc-200 rounded-lg w-full" />
                <button wire:click="addSourceUrl()" class="bg-secondary text-white p-1 rounded-full">
                    <x-icon name="plus" width="24" height="24" />
                </button>
            </div>
            @endif
            @if ($errors->has('tempSourceUrl'))
            <span class="text-red-500 text-sm">{{ $errors->first('tempSourceUrl') }}</span>
            @endif
            @if ($errors->has('sourceUrls'))
            <span class="text-red-500 text-sm">{{ $errors->first('sourceUrls') }}</span>
            @endif
        </div>
        @endif
        <!-- END: Source URLs -->

        <!-- Context and Image -->
        <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.context')}}:</label>
                    <div>{{__('blog.briefly_describe', ['minWords' => '30'])}}</div>
                    <div>{{__('blog.paste_content', ['maxChars' => '30000'])}}</div>
                </div>
                <textarea class="border border-zinc-200 rounded-lg" rows="5" maxlength="30000"
                    wire:model.live="context"></textarea>
                @if($errors->has('context'))
                <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                @endif
            </div>

            <!-- Hero Image -->
            <div class="w-full flex flex-col gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label class="text-xl font-bold text-gray-700">{{__('blog.generate_hero_image')}}:</label>
                    </div>
                    <div class="md:col-span-1">
                        <x-checkbox md id="generate_img" name="generate_img" label="{{__('common.yes')}}"
                            wire:model.live="generateImage" />
                    </div>
                </div>
                @if($generateImage)
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col lg:flex-row gap-2 lg:gap-8 lg:items-center">
                        <div class="flex gap-2 items-center">
                            <label
                                class="text-lg font-medium text-gray-700">{{__('blog.hero_image_description')}}:</label>
                            @include('livewire.common.help-item', [
                            'header' => __('instructions.hero_image_tips'),
                            'content' => App\Helpers\InstructionsHelper::heroImages()
                            ])
                        </div>
                        <div class="self-start bg-gray-200 px-3 py-1 rounded-lg text-sm font-semibold text-gray-700">
                            1 {{ strtolower(__('common.image'))}} = 1 {{__('common.unit')}}
                        </div>
                    </div>
                    <div class="w-full relative flex flex-col gap-3">
                        <textarea placeholder="{{__('blog.placeholder_example')}}"
                            class="border border-zinc-200 rounded-lg w-full" rows="3" maxlength="2000"
                            wire:model.live="imgPrompt"></textarea>
                        @if($errors->has('imgPrompt'))
                        <span class="text-red-500 text-sm">{{ $errors->first('imgPrompt') }}</span>
                        @endif
                        <!-- Image guidelines -->
                        <div class="relative place-self-start md:place-self-end" x-data="{ open: false }"
                            @click.away="open = false">
                            <button @click="open = true"
                                class="h-8 2-8 flex gap-2 items-center bg-gray-500 text-white px-3 rounded-lg">
                                <x-icon name="exclamation" class="h-5 w-5" />
                                <span class="text-sm">{{ __('instructions.check_img_guidelines')}}</span>
                            </button>

                            <div class="absolute overflow-auto max-h-96 flex flex-col gap-4 bg-white border shadow-lg p-4 w-80 rounded-lg z-40"
                                x-show="open" @mouseover="open = true" style="display: none;">
                                <div class="flex gap-2 items-center justify-end w-full">
                                    <div class="font-bold text-lg">{{ __('instructions.rejected_requests') }}:</div>
                                    <x-button icon="x" sm @click="open = false" />
                                </div>
                                <div>
                                    {!! App\Helpers\InstructionsHelper::imageGuidelines() !!}
                                </div>
                            </div>
                        </div>
                        <!-- END: Image guidelines -->
                    </div>
                </div>
                @endif
            </div>
            <!-- End: Hero Image -->
        </div>
        <!-- END: Context and Image -->

        <!-- Keyword, Writing style and Tone -->
        <div class=" w-full flex flex-col md:grid md:grid-cols-3 gap-6">
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.keyword')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.keyword'),
                    'content' => App\Helpers\InstructionsHelper::blogKeyword()
                    ])
                </div>
                <input name="keyword" wire:model.live="keyword" class="p-3 rounded-lg border border-zinc-200" />
                @if($errors->has('keyword'))
                <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                @endif
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.writing_style')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.writing_style'),
                    'content' => App\Helpers\InstructionsHelper::writingStyles()
                    ])
                </div>
                <select name="style" wire:model.live="style" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.styles-options')
                </select>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('blog.tone')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.tone'),
                    'content' => App\Helpers\InstructionsHelper::writingTones()
                    ])
                </div>
                <select name="tone" wire:model.live="tone" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.tones-options')
                </select>
            </div>
        </div>
        <!-- END: Keyword, Writing style and Tone -->

        <!-- Generate button -->
        <div class="flex justify-center mt-4">
            <button wire:click="process" wire:loading.remove
                class="bg-secondary text-xl text-white font-bold px-4 py-2 rounded-lg">
                {{__('blog.generate')}}
            </button>

            <div wire:loading wire:target="process">
                <div class="flex items-center gap-2 bg-secondary text-xl text-white font-bold px-4 py-2 rounded-lg">
                    <x-loader color="white" />
                    <span>{{__('blog.preparing')}}</span>
                </div>
            </div>
        </div>
        <!-- END: Generate button -->

    </div>
</div>