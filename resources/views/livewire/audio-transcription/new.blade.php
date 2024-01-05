<div class="flex flex-col gap-6">
    @section('header')
    @include('livewire.common.header', ['icon' => 'chat-alt', 'title' => __('transcription.new_transcription')])
    @endsection
    <div class="flex flex-col gap-6">
        <div class="col-span-3">
            <div class="flex flex-col gap-6 p-4 border-zinc-200 border rounded-lg bg-white">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-lg text-zinc-700">{{__('transcription.source')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.source'), 'content' =>
                            App\Helpers\InstructionsHelper::transcriptionSource()])
                        </div>
                        <select name="provider" wire:model="sourceType" class="p-3 rounded-lg border border-zinc-200">
                            <option value="youtube">Youtube</option>
                        </select>
                    </div>
                </div>
                @if ($sourceType === 'youtube')
                <div class="flex flex-col gap-3">
                    <label class="font-bold text-lg text-zinc-700">Youtube url:</label>
                    <input name="url" wire:model="sourceUrl" class="p-3 border border-zinc-200 rounded-lg" />
                    @if($errors->has('sourceUrl'))
                    <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
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
                        <select name="originLanguage" wire:model="originLanguage"
                            class="p-3 rounded-lg border border-zinc-200">
                            @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('originLanguage'))
                        <span class="text-red-500 text-sm">{{ $errors->first('originLanguage') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label
                                class="font-bold text-lg text-zinc-700">{{__('transcription.target_language')}}</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.target_language'),
                            'content' => App\Helpers\InstructionsHelper::transcriptionTranslate()])
                        </div>
                        <select name="targetLanguage" wire:model="targetLanguage"
                            class="p-3 rounded-lg border border-zinc-200">
                            <option value="same">{{__('transcription.no')}}</option>
                            @foreach ($languages as $option)
                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('language'))
                        <span class="text-red-500 text-sm">{{ $errors->first('targetLanguage') }}</span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label
                                class="text-xl font-bold text-gray-700">{{__('transcription.identify_speakers')}}?</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.identify_speakers'),
                            'content' => App\Helpers\InstructionsHelper::transcriptionDiarization()])
                        </div>
                        <div>
                            <x-checkbox md id="identifySpeakers" name="identifySpeakers" label="{{__('common.yes')}}"
                                wire:model="identifySpeakers" />
                        </div>
                    </div>
                    @if($identifySpeakers)
                    <div class="flex flex-col gap-3">
                        <div class="flex gap-2 items-center">
                            <label
                                class="text-xl font-bold text-gray-700">{{__('transcription.speakers_expected')}}:</label>
                            @include('livewire.common.help-item', ['header' => __('transcription.speakers_expected'),
                            'content' => App\Helpers\InstructionsHelper::expectedSpeakers()])
                        </div>
                        <div>
                            <input type="number" min="2" max="10"
                                class="w-1/3 xl:w-1/4 p-3 border border-zinc-200 rounded-lg text-center"
                                wire:model="speakersExpected" />
                        </div>
                        @if($errors->has('speakersExpected'))
                        <span class="text-red-500 text-sm">{{ $errors->first('speakersExpected') }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="flex justify-center mt-4">
                    <button wire:click="process" wire:loading.remove
                        class="bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-lg">
                        {{ __('transcription.transcript') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
