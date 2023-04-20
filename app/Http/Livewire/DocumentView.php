<?php

namespace App\Http\Livewire;

use App\Models\Document;
use Livewire\Component;
use Illuminate\Support\Str;

class DocumentView extends Component
{

    public Document $document;
    public string $content;
    public string $title;
    public string $meta_description;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->content = $document->content;
        $this->title = Str::of($document->meta['title'])->trim('"');
        $this->meta_description = Str::of($document->meta['meta_description'])->trim();
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
