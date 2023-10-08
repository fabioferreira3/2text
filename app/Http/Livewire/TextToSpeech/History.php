<?php

namespace App\Http\Livewire\TextToSpeech;

use App\Models\Document;
use App\Models\MediaFile;
use App\Models\Voice;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class History extends Component
{
    public $history;
    public $isPlaying;
    public $currentAudioFile;
    public $currentAudioUrl;

    public function getListeners()
    {
        return [
            'stop-audio' => 'stopAudio',
        ];
    }

    public function mount()
    {
        $this->history = Document::ofTextToSpeech()->get()->map(function ($document) {
            if (!$document->getLatestAudios()) {
                return null;
            }
            $mediaFiles = $document->getLatestAudios();
            return collect([
                'created_at' => $document->created_at,
                'content' => $document->content,
                'media_file' => $mediaFiles ? $mediaFiles->first() : null,
                'voice' => $mediaFiles ? Voice::findOrFail($document->getMeta('voice_id')) : null,
            ]);
        })->reject(function ($audios) {
            return !$audios['media_file'];
        }) ?? [];
        $this->isPlaying = false;
        $this->currentAudioFile = null;
        $this->currentAudioUrl = null;
    }

    public function processAudio($id)
    {
        if ($this->isPlaying) {
            $this->stopAudio();
        } else {
            $this->playAudio($id);
        }
    }

    public function playAudio($id)
    {
        $this->isPlaying = true;
        $this->dispatchBrowserEvent('play-audio', [
            'id' => $id
        ]);
    }

    public function stopAudio()
    {
        $this->dispatchBrowserEvent('stop-audio');
        $this->isPlaying = false;
    }

    public function downloadAudio()
    {
        return Storage::download($this->currentAudioFile->file_path);
    }

    public function render()
    {
        return view('livewire.text-to-speech.history');
    }
}
