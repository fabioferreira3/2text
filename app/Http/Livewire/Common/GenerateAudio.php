<?php

namespace App\Http\Livewire\Common;

use App\Enums\Language;
use App\Helpers\AudioHelper;
use App\Models\Document;
use App\Repositories\GenRepository;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GenerateAudio extends Component
{
    public Document $document;
    public $menuOpen;
    public $selectedVoice = null;
    public $voices;
    public $language;
    public bool $isProcessing;
    public $isPlaying;
    public $currentAudioFile;
    public $currentAudioUrl;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.AudioGenerated" => 'ready',
            'stop-audio' => 'stopAudio',
            'toggle-audio-menu' => 'toggleAway'
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->isProcessing = false;
        $this->currentAudioFile = $this->document->meta['audio_file'] ?? null;
        $this->currentAudioUrl = ($this->document->meta['audio_file'] ?? false) ? AudioHelper::getAudioUrl($this->document->meta['audio_file']) : null;
        $this->isPlaying = false;
        $this->menuOpen = false;
        $this->setOptions($this->language ?? $document->language->value);
    }

    public function updated()
    {
        $this->setOptions($this->language);
    }

    public function ready($params)
    {
        $this->isProcessing = false;
        $this->isPlaying = false;
        if ($params['document_id'] === $this->document->id) {
            $this->currentAudioFile = $params['audio_file'];
            $this->currentAudioUrl = AudioHelper::getAudioUrl($this->currentAudioFile);
        }
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Your audio is ready!"
        ]);
    }

    public function processAudio($id)
    {
        if ($this->isPlaying) {
            return $this->stopAudio();
        }

        $this->playAudio($id);
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
        return Storage::download($this->currentAudioFile);
    }

    public function toggleAway()
    {
        $this->stopAudio();
        $this->menuOpen = false;
    }

    public function toggle()
    {
        $this->stopAudio();
        $this->menuOpen = !$this->menuOpen;
        $this->emitSelf('refresh');
    }

    public function setOptions($language)
    {
        $language = Language::tryFrom($language);
        $this->voices = AudioHelper::getVoicesByLanguage($language);
    }

    public function render()
    {
        return view('livewire.common.generate-audio');
    }

    public function generate()
    {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => "Your audio is being generated!"
        ]);
        $this->isProcessing = true;
        $voice = $this->voices->where('value', $this->selectedVoice)->first();
        GenRepository::textToSpeech($this->document, [
            'voice' => $voice['value'],
            'iso_language' => $voice['iso'],
            'text' => $this->document->content
        ]);
    }
}
