<?php

namespace App\Http\Livewire\Blog;

use App\Enums\Language;
use App\Helpers\InstructionsHelper;
use App\Jobs\Blog\CreateBlogPost;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewPost extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public string $tone;
    public string $style;
    public string $targetHeadersCount;
    public string $instructions;
    public bool $modal;
    public string $title;

    public function __construct()
    {
        $this->title = 'New Blog Post';
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->targetHeadersCount = '3';
        $this->tone = '';
        $this->style = '';
        $this->instructions = InstructionsHelper::blogGeneral();
    }

    public function render()
    {
        return view('livewire.blog.new')->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'source_url' => 'required_if:source,youtube,website_url|url',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'targetHeadersCount' => 'required|numeric|min:1|max:15',
        'tone' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your blog post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
        'targetHeadersCount.min' => 'The minimum number of subtopics is 1.',
        'targetHeadersCount.max' => 'The maximum number of subtopics is 15.',
        'targetHeadersCount.required' => 'The number of subtopics is a required field.',
    ];

    public function setSourceInfo()
    {
        $this->instructions = InstructionsHelper::sources();
    }

    public function setKeywordInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Keyword</h2> Define a keyword so the AI may use it more often.";
    }

    public function setLanguageInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Language</h2><p>Define the main language of your blog post.</p><p>Take into account that the selected language must be the same language of the context you're using, ie: the language of the youtube video, the web page or free text you provided.</p>";
    }

    public function setSubtopicsInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Subtopics</h2><p>Define the number of subtopics of your blog post. The more subtopics, more content will be generated.<p>
        <h3 class='font-bold text-sm'>Note</h3>
        <p class='text-sm'>As an estimate, 1 subtopic covers around 350 words.</p>";
    }

    public function setStyleInfo()
    {
        $this->instructions = InstructionsHelper::writingStyles();
    }

    public function setToneInfo()
    {
        $this->instructions = InstructionsHelper::writingTones();
    }

    public function process()
    {
        $this->validate();
        CreateBlogPost::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'target_headers_count' => $this->targetHeadersCount,
                'tone' => $this->tone,
                'style' => $this->style,
                'keyword' => $this->keyword,
            ]
        ]);

        return redirect()->to('/dashboard');
    }
}
