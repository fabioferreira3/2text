<?php

namespace App\Http\Livewire\Blog;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;
use Illuminate\Support\Str;

class ContentEditor extends Component
{
    public Document $document;
    public string $content;
    public bool $copied;
    public $tone;
    protected $listeners = ['refreshContent' => 'updateContent', 'editorUpdated'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->tone = $document->meta['tone'];
        $this->content = $document->content;
    }

    public function render()
    {
        return view('livewire.blog.content-editor');
    }

    public function regenerate()
    {
        $processId = Str::uuid();
        $repo = new DocumentRepository($this->document);
        $repo->createTask(DocumentTaskEnum::EXPAND_OUTLINE, [
            'process_id' => $processId,
            'meta' => [
                'tone' => $this->tone,
            ],
            'tone' => $this->tone,
            'order' => 1
        ]);
        $repo->createTask(
            DocumentTaskEnum::EXPAND_TEXT,
            [
                'process_id' => $processId,
                'meta' => [
                    'tone' => $this->tone,
                ],
                'order' => 2
            ]
        );

        DispatchDocumentTasks::dispatch();

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Your post is being regenerated"
        ]);

        return redirect()->to('/dashboard');
    }

    public function editorUpdated($content)
    {
        $this->copied = false;
        $this->content = $content;
    }

    public function updateContent($content)
    {
        if (is_array($content)) {
            if ($content['field'] === 'content') {
                $this->content = $content['content'];
            }
            $this->emit('refreshEditor');
        } else {
            dd('eitaasd');
            $this->copied = false;
            $this->content = $content['content'];
            $this->dispatchBrowserEvent('refresh-page');
        }
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
        $this->emitUp('showHistoryModal', 'content', false);
        $this->emit('refreshEditor');
    }
}
