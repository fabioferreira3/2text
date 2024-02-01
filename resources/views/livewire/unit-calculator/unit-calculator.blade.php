<div class="p-8 flex flex-col">
    <div class="text-3xl font-bold text-gray-800">
        Units calculator
        <div class="bg-secondary h-1 w-full"></div>
    </div>
    <div class="mt-4 mb-8 text-gray-700 font-semibold">Calculate how much you could generate with a particular number
        of units</div>
    <div class="flex flex-col gap-8 items-center">
        <div class="flex flex-col items-center gap-4">
            <label for="units" class="text-gray-600 font-thin">{{__('checkout.enter_units_no_minimum')}}:</label>
            <input type="number" min="0" max="10000" wire:model="units"
                class="border border-gray-300 text-center rounded-lg focus:outline-none focus:ring-2 focus:ring-[#EA1F88] text-lg text-gray-600 px-3">
            @if($errors->has('units'))
            <span class="text-red-500 text-sm">{{ $errors->first('units') }}</span>
            @endif
            <button wire:click="processPurchase"
                class="bg-secondary px-4 py-1 rounded-xl text-white font-bold">Buy</button>
        </div>
        <div class="flex flex-col items-center justify-center gap-4 text-center border border-gray-300 rounded-xl p-8">
            <div class="flex flex-col items-center text-gray-700">
                <div class="flex items-center gap-2">
                    <x-icon name="newspaper" width="30" />
                    <div class="text-lg font-thin">{{$wordsCount}} words
                    </div>
                </div>
                <div class="text-xs text-center w-1/2 mt-1"><span class="font-semibold text-gray-900">related
                        tools:</span> Social
                    media, Blog post, Summarizer,
                    Paraphraser,
                    Insight hub</div>
            </div>
            <div class="flex items-center gap-2 text-secondary border-y-2">
                <x-icon name="switch-vertical" width="20" />
                <div class="text-xl font-semibold">or</div>
            </div>
            <div class="flex flex-col items-center text-gray-700">
                <div class="flex items-center gap-2">
                    <x-icon name="photograph" width="30" />
                    <div class="text-lg font-thin">{{$imagesCount}} images
                    </div>
                </div>
                <div class="text-xs text-center w-1/2 mt-1"><span class="font-semibold text-gray-900">related
                        tools:</span> AI image, Social media, Blog post</div>
            </div>
            <div class="flex items-center gap-2 text-secondary border-y-2">
                <x-icon name="switch-vertical" width="20" />
                <div class="text-xl font-semibold">or</div>
            </div>
            <div class="flex flex-col items-center text-gray-700">
                <div class="flex items-center gap-2">
                    <x-icon name="volume-up" width="30" />
                    <div class="text-lg font-thin">{{$transcriptionLength}} hours of audio transcriptions
                    </div>
                </div>
                <div class="text-xs mt-1"><span class="font-semibold text-gray-900">related
                        tools:</span> Transcription</div>
            </div>
            <div class="flex items-center gap-2 text-secondary border-y-2">
                <x-icon name="switch-vertical" width="20" />
                <div class="text-xl font-semibold">or</div>
            </div>
            <div class="flex flex-col items-center text-gray-700">
                <div class="flex items-center gap-2">
                    <x-icon name="microphone" width="30" />
                    <div class="text-lg font-thin">{{$audioWordsCount}} words of generated audio
                    </div>
                </div>
                <div class="text-xs mt-1"><span class="font-semibold text-gray-900">related
                        tools:</span> Text to audio</div>
            </div>
        </div>
    </div>
</div>
