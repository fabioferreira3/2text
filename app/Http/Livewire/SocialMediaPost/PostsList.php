<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Enums\DocumentStatus;
use App\Enums\Language;
use App\Models\Document;
use Livewire\Component;

class PostsList extends Component
{
    public Document $document;
    public string $content;
    public bool $displayHistory = false;
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
    public bool $showInstructions;
    public bool $modal;
    public $title;

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

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your social media post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your social media post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
    ];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->showInstructions = $document->status == DocumentStatus::ON_HOLD ? true : false;
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->tone = 'default';
        $this->style = 'default';
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
        return view('livewire.social-media-post.posts-list');
    }

    public function process()
    {
        $this->validate();
    }

    public function toggleInstructions()
    {
        $this->showInstructions = !$this->showInstructions;
    }
}
