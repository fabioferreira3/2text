<?php

namespace App\Http\Livewire\TextTranscription;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\TextTranscription\CreateTranscription;
use App\Repositories\DocumentRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTranscription extends Component
{
    use Actions;

    public string $source_url;
    public string $source;
    public string $origin_language;
    public string $target_language;
    public array $languages;
    public bool $modal;
    public string $title;

    public function __construct()
    {
        $this->title = 'New Transcription';
        $this->source = 'youtube';
        $this->source_url = '';
        $this->origin_language = 'en';
        $this->target_language = 'same';
        $this->languages = Language::getLabels();
    }

    public function render()
    {
        return view('livewire.text-transcription.new')->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:youtube',
        'source_url' => 'required|url',
        'origin_language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'target_language' => 'required|in:same,en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr'
    ];

    protected $messages = [
        'source_url.required' => 'You need to provide a Youtube link for the transcription.',
        'source.required' => 'Source is a required field.',
        'origin_language.required' => 'Source language is a required field.',
        'target_language.required' => 'Target language is a required field.',
    ];

    public function process()
    {
        $this->validate();
        $repo = new DocumentRepository();
        $document = $repo->createGeneric([
            'type' => DocumentType::TEXT_TRANSCRIPTION->value,
            'source' => $this->source,
            'language' => $this->origin_language,
            'meta' => [
                'source_url' => $this->source_url
            ]
        ]);

        CreateTranscription::dispatch($document, [
            'target_language' => $this->target_language
        ]);

        return redirect()->to('/dashboard');
    }

    public function updated()
    {
        if ($this->target_language === $this->origin_language) {
            $this->target_language = 'same';
        }
    }
}
