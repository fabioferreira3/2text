<?php

namespace App\Http\Livewire\Summarizer;

use App\Models\Document;
use App\Models\DocumentContentBlock;
use Livewire\Component;

class SummaryView extends Component
{
    public Document $document;
    public DocumentContentBlock $contentBlock;

    public function mount(Document $document)
    {
        $this->contentBlock = $document->contentBlocks()->ofTextType()->first();
    }

    public function render()
    {
        return view('livewire.summarizer.summary');
    }
}
