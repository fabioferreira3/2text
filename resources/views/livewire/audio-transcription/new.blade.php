<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'chat-alt', 'label' => __('transcription.new_transcription')])

    <div class="flex flex-col gap-6">
        <div class="col-span-3">
            <div class="flex flex-col gap-4 p-4 border-zinc-200 border rounded-lg bg-white">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('transcription.source')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.source'), 'content' =>
                            App\Helpers\InstructionsHelper::transcriptionSource()])
                        </div>
                        <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                            <option value="youtube">Youtube</option>
                        </select>
                    </div>
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
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label
                                class="font-bold text-lg text-zinc-700">{{__('transcription.origin_language')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.origin_language'),
                            'content' => App\Helpers\InstructionsHelper::transcriptionLanguage()])
                        </div>
                        <select name="origin_language" wire:model="origin_language"
                            class="p-3 rounded-lg border border-zinc-200">
                            @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('language'))
                        <span class="text-red-500 text-sm">{{ $errors->first('origin_language') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label
                                class="font-bold text-lg text-zinc-700">{{__('transcription.target_language')}}</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.target_language'),
                            'content' => App\Helpers\InstructionsHelper::transcriptionTranslate()])
                        </div>
                        <select name="target_language" wire:model="target_language"
                            class="p-3 rounded-lg border border-zinc-200">
                            <option value="same">{{__('transcription.no')}}</option>
                            @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('language'))
                        <span class="text-red-500 text-sm">{{ $errors->first('target_language') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-center mt-4">
                    <button wire:click="process" wire:loading.remove
                        class="bg-secondary hover:bg-main text-white font-bold px-4 py-2 rounded-lg">
                        {{ __('transcription.transcript') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>