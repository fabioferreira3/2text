<?php

namespace App\Http\Livewire\AudioTranscription;

use App\Models\Document;
use Livewire\Component;

class AudioTranscription extends Component
{
    public Document $document;
    public $contentBlocks;
    public bool $isProcessing = false;

    protected $listeners = ['refreshContent' => 'updateContent', 'editorUpdated', 'closeHistoryModal', 'refresh'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->contentBlocks = $document->contentBlocks()->ofTextType()->get();
    }

    public function render()
    {
        return view('livewire.audio-transcription.transcription');
    }
}
