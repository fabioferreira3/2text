<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'switch-horizontal', 'label' => __('paraphraser.new_paraphrase')])
    <div class="flex flex-col">
        <div class="flex flex-col gap-6 p-4 border rounded-lg">
            <div class="w-full flex flex-col md:grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2 items-center">
                        <label>{{__('paraphraser.origin')}}:</label>
                        @include('livewire.common.help-item', ['header' => __('paraphraser.origin'), 'content' => App\Helpers\InstructionsHelper::paraphraserSources()])
                    </div>
                    <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                        @include('livewire.common.source-providers-options')
                    </select>
                    <button wire:click="start" wire:loading.remove class="bg-secondary text-white font-bold px-4 py-2 rounded-lg">
                        {{__('paraphraser.start')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
