<?php

namespace App\Livewire\AudioTranscription;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\AudioTranscription\CreateTranscription;
use App\Repositories\DocumentRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTranscription extends Component
{
    use Actions;

    public string $sourceUrl;
    public string $sourceType;
    public string $originLanguage;
    public string $targetLanguage;
    public array $languages;
    public bool $modal;
    public bool $identifySpeakers;
    public $speakersExpected;
    public string $title;

    public function __construct()
    {
        $this->title = 'New Transcription';
        $this->sourceType = 'youtube';
        $this->sourceUrl = '';
        $this->originLanguage = 'en';
        $this->targetLanguage = 'same';
        $this->identifySpeakers = false;
        $this->speakersExpected = null;
        $this->languages = Language::getLabels();
    }

    public function render()
    {
        return view('livewire.audio-transcription.new')->title($this->title);
    }

    public function rules()
    {
        return [
            'sourceType' => 'required|in:youtube',
            'sourceUrl' => ['required', 'url', $this->sourceType === 'youtube' ? new \App\Rules\YouTubeUrl() : ''],
            'originLanguage' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'targetLanguage' => 'required|in:same,en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'speakersExpected' => 'required_if:identifySpeakers,true|nullable|integer|min:2|max:10'
        ];
    }

    public function messages()
    {
        return [
            'sourceUrl.required' => __('validation.youtube_link_required'),
            'sourceType.required' => __('validation.source_required'),
            'originLanguage.required' => __('validation.source_language_required'),
            'targetLanguage.required' => __('validation.target_language_required'),
            'speakersExpected.required_if' => __('validation.speakers_expected_required_if'),
        ];
    }

    public function process()
    {
        $this->validate();
        $targetLanguage = null;

        if ($this->targetLanguage !== 'same') {
            $targetLanguage = Language::tryFrom($this->targetLanguage)->label();
        }

        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
            'source' => $this->sourceType,
            'language' => $this->originLanguage,
            'meta' => [
                'source_url' => $this->sourceUrl,
                'identify_speakers' => $this->identifySpeakers,
                'speakers_expected' => $this->speakersExpected,
                'target_language' => $targetLanguage
            ]
        ]);

        CreateTranscription::dispatch($document, []);

        return redirect()->route('transcription-dashboard');
    }

    public function updated()
    {
        if ($this->targetLanguage === $this->originLanguage) {
            $this->targetLanguage = 'same';
        }
    }

    public function updatedIdentifySpeakers($value)
    {
        $this->speakersExpected = $value ? ($this->speakersExpected ?? 2) : null;
    }
}
