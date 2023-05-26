<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class ContentEditor extends Component
{
    public Document $document;
    public string $content;
    public bool $copied;
    protected $listeners = ['updateContent'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->content = $document->content;
    }

    public function render()
    {
        return view('livewire.blog.content-editor');
    }

    public function updateContent($newContent)
    {
        $this->copied = false;
        $this->content = $newContent;
    }

    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
        $this->emit('refreshEditor');
    }

    public function save()
    {
        $repo = new DocumentRepository($this->document);

        $this->document->update(['content' => $this->content]);
        $repo->addHistory(['field' => 'content', 'content' => $this->content]);
        $this->emit('refreshEditor');

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Content saved!"
        ]);
    }

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', 'content');
        $this->emit('refreshEditor');
    }
}
