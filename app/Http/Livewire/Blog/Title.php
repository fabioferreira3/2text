<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\GenRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class Title extends Component
{
    public string $content;
    public Document $document;
    public string $initialContent;
    public bool $copied = false;
    protected $listeners = ['refreshContent' => 'updateContent'];
    public bool $isProcessing = false;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->setContent($document);
        $this->initialContent = $this->content;
    }

    private function setContent(Document $document)
    {
        $this->content = Str::of($document->title)->trim('"');
    }

    public function render()
    {
        return view('livewire.blog.title');
    }

    public function regenerate()
    {
        try {
            GenRepository::generateTitle($this->document, $this->document->normalized_structure);
            $this->setContent($this->document->refresh());
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "New title generated!"
            ]);
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error generating a new title!"
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
        if ($this->content === $this->initialContent) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'info',
                'message' => "No changes needed to be saved"
            ]);
            return;
        }
        $this->emitUp('saveField', ['field' => 'title', 'content' => $this->content]);
        $this->initialContent = $this->content;
    }

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', 'title');
    }

    public function updateContent($params)
    {
        if ($params['field'] === 'title') {
            $this->content = Str::of($params['content'])->trim('"');
        }
    }
}
