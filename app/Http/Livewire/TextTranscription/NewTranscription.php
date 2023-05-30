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
    public string $instructions;
    public bool $modal;

    public function __construct()
    {
        $this->source = 'youtube';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->instructions = '<p>Please fill out the following information to help our AI generate a unique and high-quality blog post for you.</p>';
    }

    public function render()
    {
        return view('livewire.text-transcription.new');
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

    public function setSourceInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Source</h2> Define the Youtube link that you want me to transcribe for you";
    }

    public function setLanguageInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Language</h2><p>You need to select the language of the video since I'm currently not able to auto-discover its main language.</p>";
    }

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
