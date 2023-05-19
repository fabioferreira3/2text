<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
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

    public function saveContent()
    {
        $this->document->update(['content' => $this->content]);
        $this->emit('refreshEditor');

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Content saved!"
        ]);
    }
}
