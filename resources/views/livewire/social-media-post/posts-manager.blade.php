<div class="flex flex-col">
    @include('livewire.common.header', [
    'icon' => 'hashtag',
    'label' =>
    $document->status->value == 'draft'
    ? __('social_media.new_social_media_post')
    : __('social_media.social_media_post'),
    'suffix' => $document->title,
    ])
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="{{ $generating ? 'flex' : 'hidden' }} flex flex-col mt-8 border-1 border rounded-lg bg-white p-8">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <x-loader height="10" width="10" />
                <label class="font-bold text-zinc-700 text-2xl cursor-pointer">
                    {{ __('social_media.generating') }}<span id="typewriter"></span>
                </label>
            </div>
        </div>
    </div>

    @if (!$generating)
    <div class="flex flex-col mt-8 border-1 border rounded-xl bg-white p-8">
        <div class="flex justify-between items-center cursor-pointer h-full" wire:click="toggleInstructions">
            @include('livewire.common.label', ['title' => __('social_media.instructions')])
            <div>
                <x-icon :name="$showInstructions ? 'arrow-circle-up' : 'arrow-circle-down'" class="w-8 h-8 text-zinc-500" />
            </div>
        </div>
        @if ($showInstructions)
        <div class="pt-2 border-t mt-4">
            <div class="flex flex-col md:grid md:grid-cols-2 gap-8 mt-2">
                {{-- Col 1 --}}
                <div class="w-full flex flex-col gap-6">
                    <div class="flex flex-col gap-3">
                        {{-- Platforms --}}
                        <div>
                            <div class="flex gap-2 items-center">
                                <label class="font-bold text-lg text-zinc-700">{{ __('social_media.target_platforms')
                                    }}:</label>
                                @include('livewire.common.help-item', [
                                'header' => __('social_media.target_platforms'),
                                'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms(),
                                ])
                            </div>
                            <div class='grid grid-cols-2 gap-8 mt-2'>
                                <div class='flex flex-col gap-2'>
                                    <x-checkbox md id="facebook" name="facebook" label="Facebook" wire:model.defer="platforms.Facebook" />
                                    <x-checkbox md id="instagram" name="instagram" label="Instagram" wire:model.defer="platforms.Instagram" />
                                    <x-checkbox md id="twitter" name="twitter" label="X (former Twitter)" wire:model.defer="platforms.Twitter" />
                                </div>
                                <div class='flex flex-col gap-2'>
                                    <x-checkbox md id="linkedin" name="linkedin" label="Linkedin" wire:model.defer="platforms.Linkedin" />
                                </div>
                            </div>
                            <div class="mt-2">
                                @if ($errors->has('platforms'))
                                <span class="text-red-500 text-sm">{{ $errors->first('platforms') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-6">
                        {{-- Source --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="flex gap-2 items-center">
                                    <label class="font-bold text-lg text-zinc-700">{{ __('social_media.source')
                                        }}:</label>
                                    @include('livewire.common.help-item', [
                                    'header' => __('social_media.source'),
                                    'content' => App\Helpers\InstructionsHelper::sources(),
                                    ])
                                </div>
                                <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200 w-full">
                                    @include('livewire.common.source-providers-options')
                                </select>
                                @if ($errors->has('source'))
                                <span class="text-red-500 text-sm">{{ $errors->first('source') }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="flex gap-2 items-center">
                                    <label class="font-bold text-lg text-zinc-700">{{
                                        __('social_media.target_word_count') }}:</label>
                                    @include('livewire.common.help-item', [
                                    'header' => __('social_media.source'),
                                    'content' => App\Helpers\InstructionsHelper::sources(),
                                    ])
                                </div>
                                <input type="number" name="word_count_target" wire:model.defer="wordCountTarget" class="p-3 border border-zinc-200 rounded-lg w-full" />
                                @if ($errors->has('wordCountTarget'))
                                <span class="text-red-500 text-sm">{{ $errors->first('wordCountTarget') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Context --}}
                        <div>
                            @if ($source === 'free_text')
                            <div>
                                <div class="flex flex-col gap-1">
                                    <label class="font-bold text-lg text-zinc-700">{{ __('social_media.description')
                                        }}:</label>
                                    <div class="text-sm">
                                        {{ __('social_media.describe_subject', ['maxChars' => '30000', 'minWords' =>
                                        '100']) }}
                                    </div>
                                    <div class="text-sm">{{ __('social_media.provide_guidelines') }}
                                    </div>
                                </div>
                                <textarea class="border border-zinc-200 rounded-lg w-full mt-3" rows="8" maxlength="30000" wire:model.defer="context"></textarea>
                                <div class="mt-2">
                                    @if ($errors->has('context'))
                                    <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
                            <label class="font-bold text-lg text-zinc-700">{{ __('social_media.file_option') }}</label>
                            <input type="file" name="fileInput" wire:model="fileInput" class="p-3 border border-zinc-200 rounded-lg w-full" />
                            @endif
                            @if ($errors->has('fileInput'))
                            <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
                            @endif

                            @if ($source === 'website_url' || $source === 'youtube')
                            <label class="font-bold text-lg text-zinc-700 flex items-center justify-between">
                                @if($source === 'youtube') {{ __('social_media.youtube_option') }} <span class="text-sm">{{__('social_media.max_permitted_youtube_links', ['max'
                                    => 3])}}</span>
                                @else {{ __('social_media.url_option') }} <span class="text-sm">{{__('social_media.max_permitted_urls', ['max'
                                    => 5])}}</span>
                                @endif
                            </label>
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
                                <input name="url" x-on:keydown.enter="handleEnter($event)" wire:model.defer="tempSourceUrl" class="p-3 border border-zinc-200 rounded-lg w-full" />
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
                        </div>
                        @if ($source !== 'free_text')
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="font-bold text-lg text-zinc-700">{{
                                    __('social_media.further_instructions') }}:</label>
                                <small>{{ __('social_media.provide_guidelines') }}</small>
                            </div>

                            <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="5000" wire:model.defer="moreInstructions"></textarea>
                            @if ($errors->has('more_instructions'))
                            <span class="text-red-500 text-sm">{{ $errors->first('more_instructions') }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                {{-- Col 2 --}}
                <div class="w-full flex flex-col gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="border rounded-xl p-4">
                            <div class="grid grid-cols-2">
                                <div>
                                    <div class="flex gap-2 items-center">
                                        <label class="font-bold text-lg text-zinc-700">{{
                                            __('social_media.generate_ai_image') }}:</label>
                                        @include('livewire.common.help-item', [
                                        'header' => __('social_media.target_platforms'),
                                        'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms(),
                                        ])
                                    </div>
                                    <div class="md:col-span-1 w-full">
                                        <x-checkbox md id="generate_img" name="generate_img" label="{{
                                            __('social_media.yes') }}" wire:model="generateImage" />
                                    </div>
                                </div>
                                <!-- <div class='flex flex-col gap-4 md:gap-0'>
                                    @if ($generateImage)
                                    <div class="w-full">
                                        <div class="font-bold text-lg text-zinc-700">{{ __('social_media.select_style')
                                            }}:
                                        </div>
                                        <x-custom.dropdown class="w-full" direction="down">
                                            <x-slot name="trigger">
                                                <button class="bg-gray-100 w-full p-4 text-gray-700 rounded-lg">
                                                    <div class="flex items-center gap-4">
                                                        <div>{{ $imgStyle ??
                                                            'Select' }}</div>
                                                    </div>
                                                </button>
                                            </x-slot>
                                            @foreach ($this->stylePresets as $key => $stylePreset)
                                            <x-dropdown.item class="hover:bg-gray-100"
                                                wire:click="$set('imgStyle', '{{ $stylePreset['value'] }}')"
                                                :separator="$key > 0">
                                                <div class="flex items-center gap-4 w-full">
                                                    <img class="w-20 h-20 rounded-lg" src={{ $stylePreset['image_path']
                                                        }} />
                                                    <div class="text-lg">{{ $stylePreset['label'] }}</div>
                                                </div>
                                            </x-dropdown.item>
                                            @endforeach
                                        </x-custom.dropdown>
                                        <div class="mt-2">
                                            @if ($errors->has('imgStyle'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('imgStyle') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div> -->
                            </div>

                            @if ($generateImage)
                            <div class="flex flex-col mt-2">
                                <div class="flex gap-2 items-center">
                                    <label class="font-bold text-lg text-zinc-700">{{
                                        __('social_media.image_description') }}:</label>
                                    @include('livewire.common.help-item', [
                                    'header' => __('social_media.target_platforms'),
                                    'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms(),
                                    ])
                                </div>
                                <textarea placeholder="{{__('social_media.placeholder_example')}}" class="border border-zinc-200 rounded-lg w-full mt-3" rows="3" maxlength="1000" wire:model.defer="imgPrompt"></textarea>
                                <div class="mt-2">
                                    @if ($errors->has('imgPrompt'))
                                    <span class="text-red-500 text-sm">{{ $errors->first('imgPrompt') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex gap-3 mb-2 items-center">
                                <label class="font-bold text-lg text-zinc-700">{{ __('social_media.keyword') }}:</label>
                                @include('livewire.common.help-item', [
                                'header' => __('social_media.keyword'),
                                'content' => App\Helpers\InstructionsHelper::socialMediaKeyword(),
                                ])
                            </div>
                            <input name="keyword" wire:model.defer="keyword" class="p-3 w-full rounded-lg border border-zinc-200" />
                            @if ($errors->has('keyword'))
                            <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-3">
                            <div class="flex gap-2 items-center">
                                <label class="font-bold text-lg text-zinc-700">{{ __('social_media.language')
                                    }}:</label>
                                @include('livewire.common.help-item', [
                                'header' => __('social_media.language'),
                                'content' => App\Helpers\InstructionsHelper::socialMediaLanguages(),
                                ])
                            </div>
                            <select name="language" wire:model="language" class="p-3 rounded-lg border border-zinc-200">
                                @foreach ($languages as $option)
                                <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('language'))
                            <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{ __('social_media.writing_style')
                                }}:</label>
                            @include('livewire.common.help-item', [
                            'header' => __('social_media.writing_style'),
                            'content' => App\Helpers\InstructionsHelper::writingStyles(),
                            ])
                        </div>
                        <select name="style" wire:model="style" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            @include('livewire.common.styles-options')
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{ __('social_media.tone') }}:</label>
                            @include('livewire.common.help-item', [
                            'header' => __('social_media.tone'),
                            'content' => App\Helpers\InstructionsHelper::writingTones(),
                            ])
                        </div>
                        <select name="tone" wire:model="tone" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            @include('livewire.common.tones-options')
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-8">
                <button wire:click="process" class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-lg">
                    <div wire:loading.remove>
                        <x-icon name="play" class="w-8 h-8" />
                    </div>
                    <div wire:loading>
                        <x-loader color="white" height="10" width="10" />
                    </div>
                    <span>{{__('social_media.generate')}}</span>
                </button>
            </div>

        </div>
        @endif
    </div>
    @endif
    @if (count($document->children))
    <div class="flex flex-col w-full lg:grid lg:grid-cols-2 xl:grid-cols-3 mt-6 gap-12 md:gap-6">
        @foreach ($document->children()->ofMediaPosts()->latest()->get() as $post)
        @if ($post->isFinished && $post->contentBlocks->count())
        @php $platform = $post->meta['platform']; @endphp
        @livewire("social-media-post.platforms.$platform-post", [$post], key($post->id))
        @endif
        @endforeach
    </div>
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        initTypewriter('typewriter', ['...'], 120);
    });
</script>