<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'hashtag', 'label' => 'New Social Media Post'])

    <div class="flex flex-col md:grid md:grid-cols-6 gap-6">
        <div class="col-span-2">
            <div class="p-4 bg-zinc-200 rounded-lg">
                <h2 class="font-bold text-lg">Instructions</h2>
                <div class="flex flex-col gap-2 mt-2">
                    {!! $this->instructions !!}
                </div>
            </div>
        </div>
        <div class="col-span-4">
            <div class="flex flex-col gap-8 p-4 border rounded-lg">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">Target platforms:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setPlatformsInfo()" />
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
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">Source:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setSourceInfo()" />
                        </div>
                        <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                            <option value="youtube">Youtube</option>
                            <option value="website_url">Website URL</option>
                            <option value="free_text">Free text</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">Language:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setLanguageInfo()" />
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
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">Writing style:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setStyleInfo()" />
                        </div>
                        <select name="style" wire:model="style" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            <option class="hover:bg-red-200" value="">Default</option>
                            @include('livewire.common.styles-options')
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">Tone:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setToneInfo()" />
                        </div>
                        <select name="tone" wire:model="tone" class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                            <option class="hover:bg-red-200" value="">Default</option>
                            @include('livewire.common.tones-options')
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label class="font-bold text-lg text-zinc-700">Keyword:</label>
                        <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setKeywordInfo()" />
                    </div>
                    <input name="keyword" wire:model="keyword" class="p-3 rounded-lg border border-zinc-200" />
                    @if($errors->has('keyword'))
                    <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                    @endif
                </div>
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
                @if ($source === 'website_url' || $source === 'youtube')
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="font-bold text-lg text-zinc-700">Further instructions (optional):</label>
                        <small>Feel free to provide any other guidelines so I can write a post that meets your expectation. (max 5000 chars)</small>
                    </div>

                    <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="5000" wire:model="more_instructions"></textarea>
                    @if($errors->has('more_instructions'))
                    <span class="text-red-500 text-sm">{{ $errors->first('more_instructions') }}</span>
                    @endif
                </div>
                @endif
                @if ($source === 'free_text')
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="font-bold text-lg text-zinc-700">Description:</label>
                        <small>Describe the subject of the post using at least 100 words (max. 30000 chars)</small>
                        <small>Feel free to provide any other guidelines so I can write a post that meets your expectation.</small>
                    </div>

                    <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="30000" wire:model="context"></textarea>
                    @if($errors->has('context'))
                    <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                    @endif
                </div>
                @endif
                <div class="flex justify-center mt-4">
                    <button wire:click="process" wire:loading.remove class="bg-red-700 text-white font-bold px-4 py-2 rounded-lg">
                        Generate!
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>