<div class="flex flex-col gap-6">
    @livewire('common.header', [
    'icon' => 'search-circle',
    'title' => $document->title ?? __('inquiry-hub.new'),
    'suffix' => $document->title ? __('inquiry-hub.inquiry_hub') : "",
    'document' => $document,
    'editable' => true
    ])
    <div></div>
</div>