<?php

namespace App\Http\Livewire\Blog;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class MetaDescription extends Component
{
    public Document $document;
    public string $content;
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
            "echo-private:User.$userId,.MetaDescriptionGenerated" => 'ready',
            'refreshContent' => 'updateContent',
        ];
    }

    private function setContent(Document $document)
    {
        $this->content = Str::of($document->meta['meta_description'])->trim('"');
    }

    public function regenerate()
    {
        $this->isProcessing = true;
        $repo = new DocumentRepository($this->document);
        $repo->createTask(DocumentTaskEnum::CREATE_METADESCRIPTION, []);
        DispatchDocumentTasks::dispatch($this->document);
    }

    public function ready($params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->isProcessing = false;
            $this->setContent($this->document->refresh());
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "New meta description generated!"
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
        $this->emitUp('saveField', ['field' => 'meta_description', 'content' => $this->content]);
        $this->initialContent = $this->content;
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

    public function render()
    {
        return view('livewire.blog.meta-description');
    }
}
