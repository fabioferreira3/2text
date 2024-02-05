<div class="relative">
    <button wire:click="$dispatch('closeUnitCalculator')"
        class="flex items-center h-10 w-10 rounded-full gap-1 absolute top-2 right-8 bg-gray-300 p-2 font-bold text-gray-800">
        <x-icon name="x" width="24" />
    </button>
    <div class="flex flex-col p-8">
        <div>
            <div class="text-3xl font-bold text-gray-800">Units calculator</div>
            <div class="bg-secondary h-1 w-full"></div>
        </div>
        <div class="mt-4 mb-8 text-gray-700 font-semibold">Calculate how much you could generate with a particular
            number
            of units</div>
        <div class="flex flex-col gap-8 items-center">
            <div class="flex flex-col items-center gap-4">
                <label for="units" class="text-gray-600 font-thin">{{__('checkout.enter_units_no_minimum')}}:</label>
                <input type="number" min="0" max="10000" wire:model.live="units"
                    class="border border-gray-300 text-center rounded-lg focus:outline-none focus:ring-2 focus:ring-[#EA1F88] text-lg text-gray-600 px-3">
                @if($errors->has('units'))
                <span class="text-red-500 text-sm">{{ $errors->first('units') }}</span>
                @endif
                <button wire:click="processPurchase"
                    class="bg-secondary px-4 py-1 rounded-xl text-white font-bold">Buy</button>
            </div>
            <div
                class="flex flex-col items-center w-1/2 justify-center gap-4 text-center border border-gray-300 rounded-xl p-8">
                <div class="italic text-sm">Important: The values below are just an estimation. You may check the actual
                    usage cost
                    of
                    each AI tool in their
                    respectives
                    pages. You may also spend your units across different tools. The estimation below considers a
                    scenario
                    where you would spend all
                    of your units in a single tool.</div>
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
                    <div class="font-semibold">OR</div>
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
                    <div class="font-semibold">OR</div>
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
                    <div class="font-semibold">OR</div>
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

</div>
