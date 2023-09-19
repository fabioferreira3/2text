<div class="flex flex-col p-4 bg-[#B92A70] rounded-b-lg">
    {{-- @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true]) --}}
    <div>
        <img class="rounded-t-lg" src={{$image ?? 'https://cdn3.vectorstock.com/i/1000x1000/35/52/placeholder-rgb-color-icon-vector-32173552.jpg'}}/>
        <textarea class="w-full text-base rounded-b-lg border border-zinc-200" name="text" wire:model="text" rows="12"></textarea>
    </div>

    @if ($displayHistory)
        @livewire('common.history-modal', [$document])
    @endif
</div>
