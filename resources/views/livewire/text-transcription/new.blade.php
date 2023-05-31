<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'chat-alt', 'label' => 'New Transcription'])

    <div class="flex flex-col md:grid md:grid-cols-5 gap-6">
        <div class="col-span-2">
            <div class="p-4 bg-zinc-200 rounded-lg">
                <h2 class="font-bold text-lg">Instructions</h2>
                <div class="flex flex-col gap-2 mt-2">
                    {!! $this->instructions !!}
                </div>
            </div>
        </div>
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
                        </select>
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
                            <label>Language of the video:</label>
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
                        @if($errors->has('language'))
                            <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-center mt-4">
                    <button
                        wire:click="process"
                        wire:loading.remove
                        class="bg-red-700 text-white font-bold px-4 py-2 rounded-lg"
                    >
                        Transcript!
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
