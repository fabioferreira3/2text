<div class="w-full flex flex-col">
    @section('header')
    <div class="flex items-center justify-between gap-8">
        <div class="flex items-center gap-2">
            @include('livewire.common.header', [
            'icon' => 'hashtag',
            'title' => __('social_media.social_media'),
            'suffix' => '',
            ])
            <button onclick="livewire.dispatch('invokeNew')"
                class="flex items-center gap-2 bg-secondary text-white px-4 py-1 rounded-lg">
                <span class="font-bold text-lg">{{__('social_media.new')}}</span>
            </button>
        </div>
        <div class="w-1/2">
            @include('livewire.common.page-info', ['content' => __('social_media.page_info')])
        </div>
    </div>
    @endsection

    @livewire('my-documents-table', ['documentTypes' => [\App\Enums\DocumentType::SOCIAL_MEDIA_GROUP]])
</div>
