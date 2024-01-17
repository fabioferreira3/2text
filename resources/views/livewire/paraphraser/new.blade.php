<div class="flex flex-col gap-6">
    @section('header')
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'title' => __('paraphraser.new_paraphrase')])
    @endsection
    <div class="flex flex-col">
        <div class="flex flex-col gap-6 p-4 border rounded-lg">
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-6">
                    <div class="flex gap-2 items-center">
                        <label class="font-bold font-bold text-3xl text-zinc-700">{{__('paraphraser.source')}}:</label>
                        @include('livewire.common.help-item', ['header' => __('paraphraser.source'), 'content' =>
                        App\Helpers\InstructionsHelper::paraphraserSources()])
                    </div>
                    <select name="sourceType" wire:model="sourceType" class="p-3 rounded-lg border border-zinc-200">
                        <option value="free_text">{{__('paraphraser.free_text')}}</option>
                        {{-- <option value="website_url">Website URL</option> --}}
                    </select>
                    @if ($displaySourceUrl)
                    <div class="flex flex-col gap-3">
                        <label>URL:</label>
                        <input name="url" wire:model="sourceUrl" class="p-3 border border-zinc-200 rounded-lg" />
                        @if($errors->has('sourceUrl'))
                        <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center">
                        <div class="mr-4 font-bold">{{__('paraphraser.language')}}:</div>
                        <select name="language" wire:model="language"
                            class="p-3 rounded-lg border border-zinc-200 w-64">
                            @include('livewire.common.languages-options')
                        </select>
                    </div>
                    @include('livewire.paraphraser.tones')
                    @endif
                    <button wire:click="start" @if($isProcessing) disabled @endif wire:loading.remove
                        class="bg-secondary w-1/3 m-auto mt-2 text-white font-bold px-4 py-2 rounded-lg">
                        @if ($isProcessing){{__('paraphraser.processing')}} @endif
                        @if (!$isProcessing){{__('paraphraser.start')}} @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>