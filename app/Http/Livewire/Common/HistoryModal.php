<?php

namespace App\Http\Livewire\Common;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;
use Illuminate\Support\Str;

class HistoryModal extends Component
{
    public Document $document;
    public string $field;
    public bool $isMeta;
    public string $fieldTitle;
    public $history;

    protected $listeners = ['listDocumentHistory'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->field = '';
        $this->isMeta = true;
        $this->fieldTitle = '';
        $this->history = collect([]);
    }

    public function render()
    {
        return view('livewire.common.history-modal');
    }

    public function apply($content)
    {
        $content = base64_decode($content);
        if ($this->field) {
            $repo = new DocumentRepository($this->document);
            if ($this->isMeta) {
                $repo->updateMeta($this->field, $content);
            } else {
                $this->document->update([$this->field => $content]);
            }
        }
        $this->emit('refresh', $this->field, $this->isMeta);
        $this->emit('closeHistoryModal');
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => $this->fieldTitle . ' updated!'
        ]);
    }

    public function listDocumentHistory(string $field, bool $isMeta = true)
    {
        $this->field = $field;
        $this->isMeta = $isMeta;
        $this->fieldTitle = Str::title(str_replace('_', ' ', $field));
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
