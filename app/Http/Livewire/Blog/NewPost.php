<?php

namespace App\Http\Livewire\Blog;

use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\CreateBlogPost;
use App\Repositories\DocumentRepository;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Illuminate\Validation\Rule;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewPost extends Component
{
    use Actions;

    public string $context;
    public array $sourceUrls;
    public string $source;
    public $fileInput = null;
    public string $tempSourceUrl;
    public bool $maxSourceUrlsReached;
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
        $this->title = __('blog.new_blog_post');
        $this->source = SourceProvider::FREE_TEXT->value;
        $this->context = '';
        $this->sourceUrls = [];
        $this->tempSourceUrl = '';
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
            'source' => 'required|in:free_text,youtube,website_url,pdf,csv,docx,json',
            'sourceUrls' => [
                'required_if:source,youtube,website_url',
                'array',
                function ($attribute, $value, $fail) {
                    if (request()->input('source') === 'youtube' && count($value) > 3) {
                        return $fail('The maximum number of Youtube sources is 3.');
                    }
                    if (request()->input('source') === 'website_url' && count($value) > 5) {
                        return $fail('The maximum number of source URLs is 5.');
                    }
                },
            ],
            'sourceUrls.*' => 'url',
            'keyword' => 'required',
            'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'targetHeadersCount' => 'required|numeric|min:2|max:10',
            'tone' => 'nullable',
            'style' => 'nullable',
            'fileInput' => [
                'required_if:source,docx,pdf',
                'max:51200', // in kilobytes, 50mb = 50 * 1024 = 51200kb
                new DocxFile($this->source),
                new PdfFile($this->source)
            ]
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

    public function messages()
    {
        return
            [
                'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
                'sourceUrls.required_if' => __('validation.blog_post_sourceurl_required'),
                'sourceUrls.*.url' => __('validation.active_url'),
                'keyword.required' => 'You need to provide a keyword.',
                'source' => [
                    'required',
                    Rule::in(array_map(fn ($value) => $value->value, SourceProvider::cases()))
                ],
                'language.required' => 'Language is a required field.',
                'targetHeadersCount.min' => 'The minimum number of subtopics is 2.',
                'targetHeadersCount.max' => 'The maximum number of subtopics is 10.',
                'targetHeadersCount.required' => 'The number of subtopics is a required field.',
            ];
    }

    public function process()
    {
        $this->validate($this->rules());
        $params = [
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_urls' => $this->sourceUrls ?? [],
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
