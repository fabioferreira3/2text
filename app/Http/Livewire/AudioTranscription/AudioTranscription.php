<?php

namespace App\Http\Livewire\AudioTranscription;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class AudioTranscription extends Component
{
    public Document $document;
    public $contentBlocks;
    public bool $isProcessing = false;
    public string $title;

    protected $listeners = ['refreshContent' => 'updateContent', 'editorUpdated', 'refresh'];

    public function mount(Document $document)
    {
        $this->title = $document->title;
        $this->document = $document;
        $this->contentBlocks = $document->contentBlocks()->ofTextType()->get();
    }

    public function downloadSubtitle($format)
    {
        return Storage::download($this->document->getMeta($format . '_file_path'));
    }

    public function render()
    {
        return view('livewire.audio-transcription.transcription');
    }
}
