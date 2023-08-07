<?php

namespace App\Http\Livewire\Blog;

use App\Enums\Language;
use App\Jobs\Blog\CreateBlogPost;
use App\Repositories\DocumentRepository;
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
        'targetHeadersCount' => 'required|numeric|min:2|max:15',
        'tone' => 'nullable',
        'style' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your blog post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
        'targetHeadersCount.min' => 'The minimum number of subtopics is 2.',
        'targetHeadersCount.max' => 'The maximum number of subtopics is 15.',
        'targetHeadersCount.required' => 'The number of subtopics is a required field.',
    ];

    public function process()
    {
        $this->validate();
        $params = [
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
        ];
        $repo = new DocumentRepository();
        $document = $repo->createBlogPost($params);
        CreateBlogPost::dispatch($document, $params);

        return redirect()->to('/dashboard');
    }
}
