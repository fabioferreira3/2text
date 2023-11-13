<?php

namespace App\Http\Livewire\TextToSpeech;

use App\Models\Document;
use App\Models\MediaFile;
use App\Models\Voice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class AudioHistory extends Component
{
    public $history;
    public $isPlaying;
    public $displayHeader;
    public $selectedMediaFile;

    public function getListeners()
    {
        return [
            'stop-audio' => 'stopAudio',
        ];
    }

    public function mount($displayHeader = true)
    {
        $this->refresh();
        $this->isPlaying = false;
        $this->displayHeader = $displayHeader;
        $this->selectedMediaFile = null;
    }

    public function refresh()
    {
        $this->history = Document::ofTextToSpeech()->latest()->get()->map(function ($document) {
            if (!$document->getLatestAudios()) {
                return null;
            }
            $mediaFiles = $document->getLatestAudios();
            $voice = Voice::findOrFail($document->getMeta('voice_id'));
            $mediaFile = $mediaFiles ? $mediaFiles->first() : null;
            return collect([
                'created_at' => $document->created_at->format('m/d/Y h:ia'),
                'short_content' => Str::limit($document->content, 60, '...'),
                'content' => $document->content,
                'media_file' => $mediaFile ? [
                    'id' => $mediaFile->id,
                    'url' => $mediaFile->getSignedUrl()
                ] : null,
                'voice' => [
                    'id' => $voice->id,
                    'name' => $voice->name
                ],
            ]);
        })->reject(function ($audios) {
            return !$audios['media_file'];
        }) ?? [];
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

    public function download($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        return Storage::download($mediaFile->file_path);
    }

    public function displayDeleteModal($mediaFileId)
    {
        $this->selectedMediaFile = MediaFile::findOrFail($mediaFileId);
    }

    public function delete()
    {
        $this->selectedMediaFile->delete();
        $this->selectedMediaFile = null;
        $this->refresh();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('alerts.audio_deleted')
        ]);
    }

    public function abortDeletion()
    {
        $this->selectedMediaFile = null;
    }

    public function render()
    {
        return view('livewire.text-to-speech.history');
    }
}
