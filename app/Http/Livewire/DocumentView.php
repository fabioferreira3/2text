<?php

namespace App\Http\Livewire;

use App\Models\Document;
use Livewire\Component;


class DocumentView extends Component
{

    public Document $document;
    public string $content;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->content = $document->content;
    }

    public function render()
    {
        return view('livewire.document-view');
    }

    public function updateContent($content)
    {
        $this->content = $content;
    }
}
