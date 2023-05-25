<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use Livewire\Component;

class BlogPost extends Component
{
    public Document $document;
    public bool $displayHistory = false;

    protected $listeners = ['contentUpdated', 'showHistoryModal', 'closeHistoryModal', 'refresh'];

    public function mount(Document $document)
    {
        $this->document = $document;
    }

    public function render()
    {
        return view('livewire.blog.blog-post');
    }

    public function showHistoryModal(string $field)
    {
        $this->displayHistory = true;
        $this->emit('listDocumentHistory', $field);
    }

    public function closeHistoryModal()
    {
        $this->displayHistory = false;
    }

    public function refresh($field)
    {
        $this->document->refresh();
        $this->emit('refreshContent', [
            'field' => $field,
            'content' => $this->document->meta[$field]
        ]);
    }
}
