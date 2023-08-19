<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class BlogPost extends Component
{
    public Document $document;
    public bool $displayHistory = false;
    public $title;
    public bool $isProcessing = false;

    protected $listeners = ['contentUpdated', 'showHistoryModal', 'closeHistoryModal', 'refresh', 'saveField'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->title = 'Blog post';
    }

    public function render()
    {
        return view('livewire.blog.blog-post')->layout('layouts.app', ['title' => $this->title]);
    }

    public function showHistoryModal(string $field, bool $isMeta = true)
    {
        $this->displayHistory = true;
        $this->emit('listDocumentHistory', $field, $isMeta);
    }

    public function closeHistoryModal()
    {
        $this->displayHistory = false;
    }

    public function saveField(array $params)
    {
        try {
            $fieldTitle = Str::title(str_replace('_', ' ', $params['field']));
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta($params['field'], $params['content']);
            $repo->addHistory(['field' => $params['field'], 'content' => $params['content']]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "$fieldTitle updated!"
            ]);
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error saving!"
            ]);
        }
    }

    public function refresh($field, $isMeta = true)
    {
        $this->document->refresh();
        $this->emit('refreshContent', [
            'field' => $field,
            'content' => $isMeta ? $this->document->meta[$field] : $this->document[$field]
        ]);
    }
}
