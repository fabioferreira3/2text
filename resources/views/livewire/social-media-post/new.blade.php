<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'hashtag', 'label' => __('social_media.new_social_media_post')])

    <div class="flex flex-col">
            <div class="flex flex-col gap-8 p-4 border-1 border rounded-lg bg-white">
                <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.target_platforms')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.target_platforms'), 'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms()])
                        </div>
                        <div class='grid grid-cols-2 gap-8 mt-2'>
                            <div class='flex flex-col gap-2'>
                                <x-checkbox md id="facebook" name="facebook" label="Facebook" wire:model.defer="platforms.Facebook" />
                                <x-checkbox md id="instagram" name="instagram" label="Instagram" wire:model.defer="platforms.Instagram" />
                                <x-checkbox md id="twitter" name="twitter" label="Twitter" wire:model.defer="platforms.Twitter" />
                            </div>
                            <div class='flex flex-col gap-2'>
                                <x-checkbox md id="linkedin" name="linkedin" label="Linkedin" wire:model.defer="platforms.Linkedin" />
                                <x-checkbox md id="tiktoken" name="tiktok" label="TikTok" wire:model.defer="platforms.TikTok" />
                            </div>
                        </div>
                        @if($errors->has('platforms'))
                        <span class="text-red-500 text-sm">{{ $errors->first('platforms') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.source')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.source'), 'content' => App\Helpers\InstructionsHelper::sources()])
                        </div>
                        <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                            @include('livewire.common.source-providers-options')
                        </select>
                    </div>
                </div>
                <div class="flex flex-col @if($source !== 'free_text') md:grid md:grid-cols-2 @endif gap-3">
                    <div>
                        <div class="flex gap-3 mb-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.keyword')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.keyword'), 'content' => App\Helpers\InstructionsHelper::socialMediaKeyword()])
                        </div>
                        <input name="keyword" wire:model="keyword" class="p-3 w-full rounded-lg border border-zinc-200" />
                        @if($errors->has('keyword'))
                            <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                        @endif
                    </div>
                    <div>
                        @if ($source === 'youtube')
                            <div class="flex flex-col gap-3">
                                <label class="font-bold text-lg text-zinc-700">Youtube url:</label>
                                <input name="url" wire:model="source_url" class="p-3 border border-zinc-200 rounded-lg" />
                                @if($errors->has('source_url'))
                                <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                                @endif
                            </div>
                        @endif
                        @if ($source === 'website_url')
                            <div class="flex flex-col gap-3">
                                <label class="font-bold text-lg text-zinc-700">URL:</label>
                                <input name="url" wire:model="source_url" class="p-3 border border-zinc-200 rounded-lg" />
                                @if($errors->has('source_url'))
                                <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                                @endif
                            </div>
                        @endif
                        @if ($source === 'free_text')
                            <div class="flex flex-col gap-3 mt-4">
                                <div class="flex flex-col gap-1">
                                    <label class="font-bold text-lg text-zinc-700">{{__('social_media.description')}}:</label>
                                    <small>{{__('social_media.describe_subject', ['maxChars' => '30000', 'minWords' => '100'])}}</small>
                                    <small>{{__('social_media.provide_guidelines')}}</small>
                                </div>

                                <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="30000" wire:model="context"></textarea>
                                @if($errors->has('context'))
                                <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="w-full flex flex-col md:grid md:grid-cols-3 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.language')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.language'), 'content' => App\Helpers\InstructionsHelper::socialMediaLanguages()])
                        </div>
                        <select name="language" wire:model="language" class="p-3 rounded-lg border border-zinc-200">
                            @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('language'))
                        <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.writing_style')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.writing_style'), 'content' => App\Helpers\InstructionsHelper::writingStyles()])
                        </div>
                        <select name="style" wire:model="style" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            <option class="hover:bg-red-200" value="">{{__('social_media.default')}}</option>
                            @include('livewire.common.styles-options')
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.tone')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('social_media.tone'), 'content' => App\Helpers\InstructionsHelper::writingTones()])
                        </div>
                        <select name="tone" wire:model="tone" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            <option class="hover:bg-red-200" value="">{{__('social_media.default')}}</option>
                            @include('livewire.common.tones-options')
                        </select>
                    </div>
                </div>
                @if ($source === 'website_url' || $source === 'youtube')
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-bold text-lg text-zinc-700">{{__('social_media.further_instructions')}}:</label>
                            <small>{{__('social_media.provide_guidelines')}}</small>
                        </div>

                        <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="5000" wire:model="more_instructions"></textarea>
                        @if($errors->has('more_instructions'))
                        <span class="text-red-500 text-sm">{{ $errors->first('more_instructions') }}</span>
                        @endif
                    </div>
                @endif
                <div class="flex justify-center">
                    <button wire:click="process" wire:loading.remove class="bg-secondary hover:bg-primary text-white font-bold px-4 py-2 rounded-lg">
                        {{__('social_media.generate')}}
                    </button>
                </div>
            </div>
    </div>
</div>
