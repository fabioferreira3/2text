<div class="flex flex-col p-4 bg-[#B92A70] rounded-b-lg">
    {{-- @include('livewire.common.field-actions', ['copyAction' => true, 'regenerateAction' => true, 'historyAction' => true]) --}}
    <div>
        <img class="rounded-t-lg" src={{$image ?? '/images/placeholder-social-media.jpg'}} />
        @livewire('common.blocks.text-block', ['content' => $text, 'document' => $document])
    </div>

    @if ($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif
</div>