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
    public string $sourceUrl;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public string $tone;
    public string $style;
    public string $targetHeadersCount;
    public $imgPrompt;
    public bool $modal;
    public bool $generateImage;
    public string $title;



    public function __construct()
    {
        $this->title = 'New Blog Post';
        $this->source = 'free_text';
        $this->context = '';
        $this->sourceUrl = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->generateImage = false;
        $this->imgPrompt = null;
        $this->targetHeadersCount = '3';
        $this->tone = 'default';
        $this->style = 'default';
    }

    public function render()
    {
        return view('livewire.blog.new')->layout('layouts.app', ['title' => $this->title]);
    }

    public function rules()
    {
        $rules = [
            'source' => 'required|in:free_text,youtube,website_url',
            'keyword' => 'required',
            'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'targetHeadersCount' => 'required|numeric|min:2|max:10',
            'tone' => 'nullable',
            'style' => 'nullable'
        ];

        if ($this->source === 'youtube') {
            $rules['sourceUrl'] = ['required', 'url', new \App\Rules\YouTubeUrl()];
        } elseif ($this->source === 'website_url') {
            $rules['sourceUrl'] = ['required', 'url'];
        }

        if ($this->source === 'free_text') {
            $rules['context'] = 'required';
        } else {
            $rules['context'] = 'nullable';
        }

        return $rules;
    }

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
        'sourceUrl.required_if' => 'You need to provide a link for me to use as context for your blog post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
        'targetHeadersCount.min' => 'The minimum number of subtopics is 2.',
        'targetHeadersCount.max' => 'The maximum number of subtopics is 10.',
        'targetHeadersCount.required' => 'The number of subtopics is a required field.',
    ];

    public function process()
    {
        $this->validate($this->rules());
        $params = [
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->sourceUrl,
                'target_headers_count' => $this->targetHeadersCount,
                'tone' => $this->tone,
                'style' => $this->style,
                'keyword' => $this->keyword,
                'img_prompt' => $this->imgPrompt ?? null,
                'generate_image' => $this->generateImage
            ]
        ];
        $repo = new DocumentRepository();
        $document = $repo->createBlogPost($params);
        CreateBlogPost::dispatch($document, $params);

        return redirect()->to('/dashboard');
    }
}
