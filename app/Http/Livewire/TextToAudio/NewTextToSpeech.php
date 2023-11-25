<?php

namespace App\Http\Livewire\TextToAudio;

use App\Enums\Language;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTextToAudio extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public mixed $tone;
    public mixed $style;
    public bool $linkedin;
    public array $platforms;
    public mixed $more_instructions;
    public bool $modal;
    public $title;

    public function mount()
    {
        $this->title = __('social_media.new_social_media_post');
    }

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->tone = null;
        $this->style = null;
        $this->more_instructions = null;
        $this->platforms = [
            'Linkedin' => false,
            'Facebook' => false,
            'Instagram' => false,
            'Twitter' => false
        ];
    }

    public function render()
    {
        return view('livewire.text-to-audio.text-to-audio')
            ->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'source_url' => 'required_if:source,youtube,website_url|url',
        'platforms' => 'required|array',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'tone' => 'nullable',
        'style' => 'nullable'
    ];

    public function messages()
    {
        return [
            'context.required_if' => __('validation.context_required'),
            'source_url.required_if' => __('validation.social_media_sourceurl_required'),
            'keyword.required' => __('validation.keyword_required'),
            'source.required' => __('validation.source_required'),
            'language.required' => __('validation.language_required'),
        ];
    }

    public function process()
    {
        $this->validate();
        ProcessSocialMediaPosts::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'tone' => $this->tone,
                'style' => $this->style,
                'keyword' => $this->keyword,
                'more_instructions' => $this->more_instructions,
                'platforms' => $this->platforms
            ]
        ]);

        return redirect()->to('/dashboard');
    }
}
