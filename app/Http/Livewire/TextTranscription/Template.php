<?php

namespace App\Http\Livewire\TextTranscription;

use Livewire\Component;

class Template extends Component
{
    public string $icon = 'chat-alt';
    public string $title = 'Text Transcription';
    public string $description = 'Transcription of audio to text using AI';

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/transcription/new');
    }
}
