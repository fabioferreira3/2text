<div class="flex flex-col gap-6">
    <div>
        <div class="flex flex-col gap-4">
            <h1 class="text-4xl font-bold">New blog post</h1>
        </div>
    </div>

    <div class="flex flex-col md:grid md:grid-cols-5 gap-6">
        <div class="col-span-3">
            <div class="flex flex-col gap-4 p-4 border rounded">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label>Source:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setSourceInfo()"/>
                        </div>
                        <select
                            name="provider"
                            wire:model="source"
                            class="p-3 rounded-lg border border-zinc-200"
                        >
                            <option value="youtube">Youtube</option>
                            {{-- <option value="video_file">Video file</option> --}}
                            <option value="free_text">Free text</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label>Keyword:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setKeywordInfo()"/>
                        </div>
                        <input
                            name="keyword"
                            wire:model="keyword"
                            class="p-3 rounded-lg border border-zinc-200"
                        />
                        @if($errors->has('keyword'))
                            <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                        @endif
                    </div>
                </div>
                @if ($source === 'youtube')
                    <div class="flex flex-col gap-3">
                        <label>Youtube url:</label>
                        <input
                            name="url"
                            wire:model="source_url"
                            class="p-3 border border-zinc-200 rounded-lg"
                        />
                        @if($errors->has('source_url'))
                            <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                        @endif
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label>Number of Subtopics:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setSubtopicsInfo()"/>
                        </div>
                        <input
                            type="number"
                            max="15"
                            name="target_headers_count"
                            wire:model="targetHeadersCount"
                            class="p-3 rounded-lg border border-zinc-200"
                        />
                        @if($errors->has('targetHeadersCount'))
                            <span class="text-red-500 text-sm">{{ $errors->first('targetHeadersCount') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label>Language:</label>
                            <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setLanguageInfo()"/>
                        </div>
                        <select
                            name="language"
                            wire:model="language"
                            class="p-3 rounded-lg border border-zinc-200"
                        >
                        @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>Tone:</label>
                        <x-icon solid name="question-mark-circle" class="text-zinc-500 cursor-pointer h-5 w-5" wire:click="setToneInfo()"/>
                    </div>
                    <select
                        name="tone"
                        wire:model="tone"
                        class="p-3 rounded-lg border border-zinc-200"
                    >
                        <option value="">Default</option>
                        <option value="academic">Academic</option>
                        <option value="adventurous">Adventurous</option>
                        <option value="casual">Casual</option>
                        <option value="dramatic">Dramatic</option>
                        <option value="formal">Formal</option>
                        <option value="funny">Funny</option>
                        <option value="misterious">Misterious</option>
                        <option value="pessimistic">Pessimistic</option>
                        <option value="optimistic">Optimistic</option>
                    </select>
                </div>

                @if ($source === 'free_text')
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <label>Context:</label>
                            <small>Briefly describe the main topic of the post using at least 100 words</small>
                            <small>You could also paste a larger text to be used as context</small>
                        </div>

                        <textarea
                            class="border border-zinc-200 rounded-lg"
                            rows="8"
                            maxlength="30000"
                            wire:model="context"
                        ></textarea>
                        @if($errors->has('context'))
                            <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                        @endif
                    </div>
                @endif

                <div class="flex justify-center mt-4">
                    <button
                        wire:click="process"
                        wire:loading.remove
                        class="bg-red-700 text-white font-bold px-4 py-2 rounded-lg"
                    >
                        Generate!
                    </button>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="p-4 bg-zinc-200 rounded-lg">
                <h2 class="font-bold text-lg">Instructions</h2>
                <div class="flex flex-col gap-2 mt-2">
                    {!! $this->instructions !!}
                </div>
            </div>
        </div>
    </div>
    </div>
