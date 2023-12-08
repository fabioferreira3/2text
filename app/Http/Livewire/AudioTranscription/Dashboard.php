<?php

namespace App\Http\Livewire\AudioTranscription;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.audio-transcription.dashboard')
            ->layout('layouts.app', ['title' => __('transcription.audio_transcription')]);
    }

    public function new()
    {
        return redirect()->route('new-audio-transcription');
    }
}
