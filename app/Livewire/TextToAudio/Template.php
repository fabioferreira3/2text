<?php

namespace App\Livewire\TextToAudio;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'volume-up';
        $this->title = __('templates.text_to_speech');
        $this->description = __('templates.create_text_to_speech');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/text-to-audio/new');
    }
}
