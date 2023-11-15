<?php

namespace App\Http\Livewire\AudioTranscription;

use App\Models\Document;
use Livewire\Component;

class AudioTranscription extends Component
{
    public Document $document;
    public mixed $contentBlock;
    public bool $isProcessing = false;

    protected $listeners = ['refreshContent' => 'updateContent', 'editorUpdated', 'closeHistoryModal', 'refresh'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->contentBlock = $document->contentBlocks()->where('type', 'text')->first();
    }

    public function render()
    {
        return view('livewire.text-transcription.transcription');
    }
}
