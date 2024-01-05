<div class="w-full flex flex-col">
    @section('header')
    <div class="flex items-center gap-4">
        @include('livewire.common.header', [
        'icon' => 'search-circle',
        'title' => __('insight-hub.insight_hub'),
        'suffix' => '',
        ])
        <button onclick="livewire.emit('invokeNew')"
            class="flex items-center gap-2 bg-secondary text-white px-4 py-1 rounded-lg">
            <span class="font-bold text-lg">{{__('insight-hub.new')}}</span>
        </button>
    </div>
    @endsection
    @livewire('my-documents-table', ['documentTypes' => [\App\Enums\DocumentType::INQUIRY]])
    @livewire('help.help', ['title' => __('insight-hub.insight_hub')])
</div>
