<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;
use Illuminate\Support\Str;

class HistoryModal extends Component
{
    public Document $document;
    public string $field;
    public $history;

    protected $listeners = ['listDocumentHistory'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->field = '';
        $this->history = collect([]);
    }

    public function render()
    {
        return view('livewire.blog.history-modal');
    }

    public function apply($content)
    {
        $fieldTitle = str_replace('_', ' ', $this->field);
        if ($this->field) {
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta($this->field, $content);
        }
        $this->emit('refresh', $this->field);
        $this->emit('closeHistoryModal');
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => Str::title($fieldTitle) . ' updated!'
        ]);
    }

    public function listDocumentHistory($field)
    {
        $this->field = $field;
        $rawHistory = $this->document->history()->ofField($field)->get();
        $this->history = $rawHistory->map(function ($item) {
            return [
                'id' => $item->id,
                'content' => $item->content,
                'word_count' => $item->word_count,
                'created_at' => $item->created_at->format('Y-m-d / h:ia')
            ];
        });
    }
}
