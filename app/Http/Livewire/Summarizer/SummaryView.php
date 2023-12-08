<?php

namespace App\Http\Livewire\Summarizer;

use App\Models\Document;
use App\Models\DocumentContentBlock;
use Livewire\Component;

class SummaryView extends Component
{
    public Document $document;
    public DocumentContentBlock $contentBlock;
    public string $source;
    public string $title;
    public $context;

    public function mount(Document $document)
    {
        $this->title = $document->title ?? __('summarizer.summary');
        $this->source = $document->getMeta('source');
        $this->context = $document->getMeta('context')
            ?? $document->content
            ?? $document->getMeta('source_url');
        $this->contentBlock = $document->contentBlocks()->ofTextType()->latest()->first();
    }

    public function new()
    {
        redirect()->route('new-summarizer');
    }

    public function render()
    {
        return view('livewire.summarizer.summary-view');
    }
}
