<?php

namespace App\Http\Livewire\Blog;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class Title extends Component
{
    public string $content;
    public Document $document;
    public string $initialContent;
    public bool $copied = false;
    public bool $isProcessing = false;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->setContent($document);
        $this->initialContent = $this->content;
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.TitleGenerated" => 'ready',
            'refreshContent' => 'updateContent',
        ];
    }

    public function ready($params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->isProcessing = false;
            $this->setContent($this->document->refresh());
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "New title generated!"
            ]);
        }
    }

    private function setContent(Document $document)
    {
        $this->content = Str::of($document->title)->trim('"');
    }

    public function regenerate()
    {
        $this->isProcessing = true;
        $repo = new DocumentRepository($this->document);
        $repo->createTask(DocumentTaskEnum::CREATE_TITLE, []);
        DispatchDocumentTasks::dispatch($this->document);
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

    public function render()
    {
        return view('livewire.blog.title');
    }
}
