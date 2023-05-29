<?php

namespace App\Http\Livewire\TextTranscription;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class BlogPost extends Component
{
    public Document $document;

    //protected $listeners = ['contentUpdated', 'showHistoryModal', 'closeHistoryModal', 'refresh', 'saveField'];

    public function mount(Document $document)
    {
        $this->document = $document;
    }

    public function render()
    {
        return view('livewire.text-transcription.text-transcription');
    }
}
