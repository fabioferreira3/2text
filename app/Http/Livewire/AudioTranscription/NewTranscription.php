<?php

namespace App\Http\Livewire\AudioTranscription;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\AudioTranscription\CreateTranscription;
use App\Repositories\DocumentRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTranscription extends Component
{
    use Actions;

    public string $source_url;
    public string $sourceType;
    public string $origin_language;
    public string $target_language;
    public array $languages;
    public bool $modal;
    public bool $identify_speakers;
    public $speakers_expected;
    public string $title;

    public function __construct()
    {
        $this->title = 'New Transcription';
        $this->sourceType = 'youtube';
        $this->source_url = '';
        $this->origin_language = 'en';
        $this->target_language = 'same';
        $this->identify_speakers = false;
        $this->speakers_expected = null;
        $this->languages = Language::getLabels();
    }

    public function render()
    {
        return view('livewire.audio-transcription.new')->layout('layouts.app', ['title' => $this->title]);
    }

    public function rules()
    {
        return [
            'sourceType' => 'required|in:youtube',
            'source_url' => ['required', 'url', $this->sourceType === 'youtube' ? new \App\Rules\YouTubeUrl() : ''],
            'origin_language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'target_language' => 'required|in:same,en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'speakers_expected' => 'required_if:identify_speakers,true|nullable|integer|min:2|max:10'
        ];
    }

    public function messages()
    {
        return [
            'source_url.required' => __('validation.youtube_link_required'),
            'sourceType.required' => __('validation.source_required'),
            'origin_language.required' => __('validation.source_language_required'),
            'target_language.required' => __('validation.target_language_required'),
            'speakers_expected.required_if' => __('validation.speakers_expected_required_if'),
        ];
    }

    public function process()
    {
        $this->validate();
        $targetLanguage = null;
        if ($this->target_language !== 'same') {
            $targetLanguage = Language::tryFrom($this->target_language)->label();
        }
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
            'source' => $this->sourceType,
            'language' => $this->origin_language,
            'meta' => [
                'source_url' => $this->source_url,
                'identify_speakers' => $this->identify_speakers,
                'speakers_expected' => $this->speakers_expected,
                'target_language' => $targetLanguage
            ]
        ]);

        CreateTranscription::dispatch($document, []);

        return redirect()->route('transcription-dashboard');
    }

    public function updated()
    {
        if ($this->target_language === $this->origin_language) {
            $this->target_language = 'same';
        }
    }

    public function updatedIdentifySpeakers($value)
    {
        $this->speakers_expected = $value ? ($this->speakers_expected ?? 2) : null;
    }
}
