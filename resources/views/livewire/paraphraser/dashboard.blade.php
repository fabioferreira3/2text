<div class="w-full flex flex-col">
    @section('header')
    <div class="flex items-center justify-between gap-8">
        <div class="flex items-center gap-2">
            @include('livewire.common.header', [
            'icon' => 'switch-horizontal',
            'title' => __('paraphraser.paraphraser'),
            'suffix' => '',
            ])
            <button onclick="livewire.emit('invokeNew')" class="flex items-center gap-2 bg-secondary text-white px-4 py-1 rounded-lg">
                <span class="font-bold text-lg">{{__('paraphraser.new')}}</span>
            </button>
        </div>
        <div class="w-1/2">
            <div class="text-right text-gray-600">{{__('paraphraser.page_info')}}</div>
        </div>
    </div>
    @endsection

    @livewire('my-documents-table', ['documentTypes' => [\App\Enums\DocumentType::PARAPHRASED_TEXT]])
</div>