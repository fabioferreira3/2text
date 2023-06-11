<?php

namespace App\Http\Livewire\TextTranscription;

use App\Enums\Language;
use App\Jobs\TextTranscription\CreateTranscription;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTranscription extends Component
{
    use Actions;

    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public bool $modal;
    public string $title;

    public function __construct()
    {
        $this->title = 'New Transcription';
        $this->source = 'youtube';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
    }

    public function render()
    {
        return view('livewire.text-transcription.new')->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:youtube',
        'source_url' => 'required|url',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr'
    ];

    protected $messages = [
        'source_url.required' => 'You need to provide a Youtube link for the transcription.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
    ];

    public function process()
    {
        $this->validate();
        CreateTranscription::dispatch([
            'source' => $this->source,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url
            ],
        ]);

        return redirect()->to('/dashboard');
    }
}
