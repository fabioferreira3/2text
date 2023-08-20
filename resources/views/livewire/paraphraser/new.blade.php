<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'label' => __('paraphraser.new_paraphrase')])
    <div class="flex flex-col">
        <div class="flex flex-col gap-6 p-4 border rounded-lg">
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-6">
                    <div class="flex gap-2 items-center">
                        <label class="font-bold text-lg">{{__('paraphraser.origin')}}:</label>
                        @include('livewire.common.help-item', ['header' => __('paraphraser.origin'), 'content' => App\Helpers\InstructionsHelper::paraphraserSources()])
                    </div>
                    <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                        {{-- @include('livewire.common.source-providers-options') --}}
                        <option value="free_text">Free Text</option>
                        <option value="website_url">Website URL</option>
                    </select>
                    @if ($displaySourceUrl)
                    <div class="flex flex-col gap-3">
                        <label>URL:</label>
                        <input name="url" wire:model="source_url" class="p-3 border border-zinc-200 rounded-lg" />
                        @if($errors->has('source_url'))
                        <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center">
                        <div class="mr-4 font-bold">Language:</div>
                        <select name="language" wire:model="language" class="p-3 rounded-lg border border-zinc-200 w-64">
                            @include('livewire.common.languages-options')
                        </select>
                    </div>
                    @include('livewire.paraphraser.tones')
                    @endif
                    <button wire:click="start" :disabled="$isProcessing" wire:loading.remove class="bg-secondary w-1/3 m-auto mt-2 text-white font-bold px-4 py-2 rounded-lg">
                        @if ($isProcessing){{__('paraphraser.processing')}} @endif
                        @if (!$isProcessing){{__('paraphraser.start')}} @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
