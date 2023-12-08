<div class="w-full h-full">
    @section('header')
    <div class="flex items-center gap-4">
        @include('livewire.common.header', [
        'icon' => 'sort-ascending',
        'title' => __('summarizer.summarizer'),
        'suffix' => '',
        ])
        <button onclick="livewire.emit('invokeNew')"
            class="flex items-center gap-2 bg-secondary text-white px-4 py-1 rounded-lg">
            <span class="font-bold text-lg">{{__('summarizer.new')}}</span>
        </button>
    </div>
    @endsection

    @livewire('my-documents-table', ['documentTypes' => [\App\Enums\DocumentType::SUMMARIZER]])
</div>