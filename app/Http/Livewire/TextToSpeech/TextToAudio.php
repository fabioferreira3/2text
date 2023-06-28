<?php

namespace App\Http\Livewire\TextToSpeech;

use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Helpers\AudioHelper;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class TextToAudio extends Component
{
    public $document;
    protected $repo;
    public $inputText = '';
    public $voices;
    public $selectedVoice;
    public $selectedVoiceObj;
    public $language = null;
    public $isProcessing;
    public $isPlaying;
    public $currentAudioFile;
    public $currentAudioUrl;
    public string $processId = '';

    protected $rules = [
        'inputText' => 'required|string',
        'language' => 'required',
        'selectedVoice' => 'required|string'
    ];

    protected $messages = [
        'selectedVoice.required' => 'You need to select a voice'
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.AudioGenerated" => 'finish',
            'stop-audio' => 'stopAudio',
        ];
    }

    public function mount($document = null)
    {
        $this->isProcessing = false;
        $this->isPlaying = false;
        $this->selectedVoice = null;
        $this->processId = '';
        $this->document = $document ? Document::findOrFail($document) : null;
        $this->language = $this->document ? $this->document->language : Language::ENGLISH;
        $this->inputText = $this->document ? $this->document->content : "";
        $this->currentAudioFile = $this->document ? ($this->document->meta['audio_file'] ?? null) : null;
        $this->currentAudioUrl = $this->currentAudioFile ? AudioHelper::getAudioUrl($this->currentAudioFile) : null;
        $this->voices = AudioHelper::getVoicesByLanguage($this->document ? $this->document->language : $this->language);
    }

    public function finish(array $params)
    {
        if ($this->document && $params['document_id'] === $this->document->id) {
            $this->isProcessing = false;
            $this->currentAudioFile = $params['audio_file'];
            $this->currentAudioUrl = AudioHelper::getAudioUrl($this->currentAudioFile);
        }
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

    public function changeLanguage()
    {
        $this->voices = AudioHelper::getVoicesByLanguage(Language::from($this->language));
    }

    public function generate()
    {
        $this->validate();
        $this->isProcessing = true;
        $voice = $this->voices->where('value', $this->selectedVoice)->first();
        if (!$this->document) {
            $repo = new DocumentRepository();
            $this->document = $repo->createTextToSpeech([
                'text' => $this->inputText,
                'voice' => $voice['value'],
                'language' => $this->language->value
            ]);
        } else {
            $this->document->update(['content' => $this->inputText]);
        }

        GenRepository::textToSpeech($this->document, [
            'voice' => $voice['value'],
            'text' => $this->inputText
        ]);
    }

    public function render()
    {
        return view('livewire.text-to-speech.text-to-speech');
    }
}
