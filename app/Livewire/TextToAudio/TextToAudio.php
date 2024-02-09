<?php

namespace App\Livewire\TextToAudio;


use App\Helpers\AudioHelper;
use App\Models\Document;
use App\Models\MediaFile;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class TextToAudio extends Component
{
    public $document;
    protected $repo;
    public $inputText = '';
    public $voices;
    public $selectedVoice;
    public $selectedVoiceObj;
    public bool $isProcessing;
    public $isPlaying;
    public $currentAudioFile;
    public $currentAudioUrl;
    public $selectedTab = 'new';
    public string $processId = '';

    protected $rules = [
        'inputText' => 'required|string',
        'selectedVoice' => 'required|uuid'
    ];

    public function messages()
    {
        return [
            'selectedVoice.required' => __('validation.selected_voice_required'),
            'inputText' => __('validation.voice_input_text_required')
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.AudioGenerated" => 'onProcessFinished',
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
        $this->inputText = $this->document ? $this->document->content : "";
        $this->currentAudioFile = null;
        $this->currentAudioUrl = null;
        $this->voices = AudioHelper::getVoices();
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
        if ($id !== 'listen_current_audio') {
            $this->selectedVoice = $id;
        }
        $this->isPlaying = true;
        $this->dispatch('play-audio', id: $id);
    }

    public function stopAudio()
    {
        $this->isPlaying = false;
    }

    public function downloadAudio()
    {
        return Storage::download($this->currentAudioFile->file_path);
    }

    public function generate()
    {
        $this->validate();
        $this->isProcessing = true;
        $this->processId = Str::uuid();
        $document = DocumentRepository::createTextToAudio([
            'input_text' => $this->inputText,
            'voice_id' => $this->selectedVoice,
        ]);

        $genRepo = new GenRepository();
        $genRepo->registerTextToAudioTask($document, [
            'voice_id' => $this->selectedVoice,
            'process_id' => $this->processId,
            'input_text' => $this->inputText
        ]);
    }

    public function onProcessFinished(array $params)
    {
        if ($params['process_id'] === $this->processId) {
            $mediaFile = MediaFile::findOrFail($params['media_file_id']);
            $this->isProcessing = false;
            $this->currentAudioFile = $mediaFile;
            $this->currentAudioUrl = $mediaFile->getSignedUrl();
            $this->dispatch(
                'info',
                type: 'success',
                message: __('alerts.audio_file_generated')
            );
        }
    }

    public function render()
    {
        return view('livewire.text-to-audio.text-to-audio')
            ->title(__('text-to-audio.text_to_audio'));
    }
}
