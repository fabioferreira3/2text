<?php

namespace App\Http\Livewire;

use App\Models\Document;
use App\Repositories\DocumentRepository;
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

    public function regenerateTitle()
    {
        dd('eita');
    }

    public function saveTitle()
    {
        $repo = new DocumentRepository($this->document);
        $repo->updateMeta('title', $this->title);
        session()->flash('message', 'Title saved.');
    }

    public function updateContent($content)
    {
        $this->content = $content;
    }
}
