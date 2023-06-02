<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class Post extends Component
{
    public Document $document;
    public string $content;
    public bool $displayHistory = false;

    protected $listeners = ['refreshContent' => 'updateContent', 'editorUpdated', 'closeHistoryModal', 'refresh'];

    public function mount(Document $document)
    {
        $this->document = $document;
    }

    public function render()
    {
        return view('livewire.social-media-post.post');
    }

    public function showHistoryModal()
    {
        $this->displayHistory = true;
        $this->emit('listDocumentHistory', 'content', false);
    }

    public function closeHistoryModal()
    {
        $this->displayHistory = false;
    }

    public function updateContent($content)
    {
        if (is_array($content)) {
            if ($content['field'] === 'content') {
                $this->content = $content['content'];
            }
            $this->emit('refreshEditor');
        } else {
            $this->content = $content['content'];
            $this->dispatchBrowserEvent('refresh-page');
        }
    }

    public function editorUpdated($content)
    {
        $this->content = $content;
    }

    public function save()
    {
        $repo = new DocumentRepository($this->document);

        $this->document->update(['content' => $this->content]);
        $repo->addHistory(
            [
                'field' => 'content',
                'content' => $this->content
            ]
        );
        $this->emit('refreshEditor');

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Content saved!"
        ]);
    }

    public function refresh($field, $isMeta)
    {
        $this->document->refresh();
        $this->emit('refreshContent', [
            'field' => 'content',
            'content' => $this->document->content
        ]);
    }
}
