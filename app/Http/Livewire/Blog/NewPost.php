<?php

namespace App\Http\Livewire\Blog;

use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\CreateBlogPost;
use App\Repositories\DocumentRepository;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use WireUi\Traits\Actions;
use Livewire\Component;
use Livewire\WithFileUploads;

class NewPost extends Component
{
    use Actions, WithFileUploads;

    public mixed $context;
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
        $this->context = null;
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
        return [
            'context' => ['required', 'string']
        ];
    }

    public function process()
    {
        try {
            $this->validate();

            $filePath = null;
            if ($this->fileInput) {
                $accountId = Auth::check() ? Auth::user()->account_id : 'guest';
                $filename = Str::uuid() . '.' . $this->fileInput->getClientOriginalExtension();
                $filePath = "documents/$accountId/" . $filename;
                $this->fileInput->storeAs("documents/$accountId", $filename, 's3');
            }

            $params = [
                'source' => $this->source,
                'context' => $this->context,
                'language' => $this->language,
                'meta' => [
                    'source_urls' => $this->sourceUrls ?? [],
                    'source_file_path' => $filePath ?? null,
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

            return redirect()->route('blog-post-processing-view', ['document' => $document]);
        } catch (\Throwable $th) {
            $this->addError('sourceUrls', $th->getMessage());
            return;
        }
    }

    public function checkMaxSourceUrls()
    {
        $isMaxReached = false;

        if (($this->source === SourceProvider::YOUTUBE->value && count($this->sourceUrls) >= 3) ||
            ($this->source === SourceProvider::WEBSITE_URL->value && count($this->sourceUrls) >= 5)
        ) {
            $isMaxReached = true;
        }

        $this->maxSourceUrlsReached = $isMaxReached;
    }

    public function addSourceUrl()
    {
        if ($this->source === SourceProvider::YOUTUBE->value) {
            $validator = Validator::make(
                ['url' => $this->tempSourceUrl],
                [
                    'url' => [
                        'required',
                        'url',
                        function ($attribute, $value, $fail) {
                            // Check if it's a valid YouTube URL
                            if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/', $value)) {
                                return $fail('The ' . $attribute . ' must be a valid YouTube URL.');
                            }
                        },
                    ],
                ]
            );
            $validationMsg = 'This is not a valid Youtube URL.';
        } else {
            $validator = Validator::make(['url' => $this->tempSourceUrl], [
                'url' => 'required|url',
            ]);
            $validationMsg = 'The URL is not valid.';
        }

        if ($validator->fails()) {
            $this->addError('tempSourceUrl', $validationMsg);
            return;
        }

        if (!in_array($this->tempSourceUrl, $this->sourceUrls, true)) {
            $this->sourceUrls[] = $this->tempSourceUrl;
        }

        $this->tempSourceUrl = '';
        $this->checkMaxSourceUrls();
    }

    public function removeSourceUrl(string $sourceUrl)
    {
        $this->sourceUrls = array_filter($this->sourceUrls, function ($url) use ($sourceUrl) {
            return $url !== $sourceUrl;
        });

        $this->sourceUrls = array_values($this->sourceUrls);
        $this->checkMaxSourceUrls();
    }
}
