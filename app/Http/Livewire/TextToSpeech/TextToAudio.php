<?php

namespace App\Http\Livewire\TextToSpeech;

use App\Enums\Language;
use App\Helpers\AudioHelper;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class TextToAudio extends Component
{
    public $document;
    protected $repo;
    public $inputText = '';
    public $voices;
    public $selectedVoice;
    public $language = null;
    public $isSaving;
    public $isPlaying;
    public string $processId = '';

    protected $rules = [
        'inputText' => 'required|string',
        'language' => 'required'
    ];

    // public function getListeners()
    // {
    //     $userId = Auth::user()->id;
    //     return [
    //         "echo-private:User.$userId,.TextParaphrased" => 'notify',
    //         "echo-private:User.$userId,.ProcessFinished" => 'processFinished',
    //     ];
    // }

    public function mount($document = null)
    {
        $this->isSaving = false;
        $this->isPlaying = false;
        $this->processId = '';
        $this->document = $document;
        $this->language = $document ? $document->language : Language::ENGLISH;
        $this->voices = AudioHelper::getVoicesByLanguage($document ? $document->language : $this->language);
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

    public function changeVoice()
    {
        //$this->selectedVoiceObj = $this->voices->where('value', $this->selectedVoice)->first();
    }

    public function generate()
    {
        $this->validate();
        GenRepository::textToSpeech($this->document, [
            'voice' => $this->selectedVoiceObj['value'],
            'text' => $this->document->content
        ]);
    }

    public function render()
    {
        return view('livewire.text-to-speech.text-to-speech');
    }
}
