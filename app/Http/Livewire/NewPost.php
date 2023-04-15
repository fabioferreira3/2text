<?php

namespace App\Http\Livewire;

use App\Enums\DocumentType;
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
    public string $keyword;
    public string $tone;
    public string $targetHeadersCount;
    public bool $modal;

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->keyword = '';
        $this->targetHeadersCount = '3';
        $this->tone = '';
    }

    public function render()
    {
        return view('livewire.blog.new');
    }

    public function process()
    {
        CreateBlogPost::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'target_headers_count' => $this->targetHeadersCount,
                'tone' => $this->tone,
                'keyword' => $this->keyword,
            ],
            'type' => DocumentType::BLOG_POST->value,
        ]);

        return redirect()->to('/dashboard');
    }
}
