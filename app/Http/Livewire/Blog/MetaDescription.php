<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\GenRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class MetaDescription extends Component
{
    public Document $document;
    public string $content;
    public string $initialContent;
    public bool $copied = false;
    protected $listeners = ['refreshContent' => 'updateContent'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->setContent($document);
        $this->initialContent = $this->content;
    }

    private function setContent(Document $document)
    {
        $this->content = Str::of($document->meta['meta_description'])->trim('"');
    }

    public function render()
    {
        return view('livewire.blog.meta-description');
    }

    public function regenerate()
    {
        try {
            GenRepository::generateMetaDescription($this->document, [
                'tone' => $this->document->meta['tone'],
                'keyword' => $this->document->meta['keyword']
            ]);
            $this->setContent($this->document->refresh());
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "New meta description generated!"
            ]);
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error generating a new meta description!"
            ]);
        }
    }


    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
    }

    public function save()
    {
        if ($this->content !== $this->initialContent) {
            $this->emitUp('saveField', ['field' => 'meta_description', 'content' => $this->content]);
            $this->initialContent = $this->content;
        }
    }

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', 'meta_description');
    }

    public function updateContent($params)
    {
        if ($params['field'] === 'meta_description') {
            $this->setContent($this->document);
        }
    }
}
