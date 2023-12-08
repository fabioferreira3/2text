<div class="w-full flex flex-col">
    @section('header')
    <div class="flex items-center gap-4">
        @include('livewire.common.header', [
        'icon' => 'search-circle',
        'title' => __('inquiry-hub.inquiry_hub'),
        'suffix' => '',
        ])
        <button onclick="livewire.emit('invokeNew')"
            class="flex items-center gap-2 bg-secondary text-white px-4 py-1 rounded-lg">
            <span class="font-bold text-lg">{{__('inquiry-hub.new')}}</span>
        </button>
    </div>
    @endsection

    <div class="bg-zinc-100 rounded-lg px-4 pb-4 pt-4 border border-zinc-200">
        @livewire('my-documents-table', ['documentTypes' => [\App\Enums\DocumentType::INQUIRY]])
    </div>
</div>