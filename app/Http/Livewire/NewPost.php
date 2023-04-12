<?php

namespace App\Http\Livewire;

use App\Enums\DocumentType;
use App\Jobs\Blog\CreateBlogPost;
use App\Jobs\ProcessTextRequest;
use App\Repositories\TextRequestRepository;
use Livewire\Component;

class NewPost extends Component
{
    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public string $keyword;
    public string $tone;
    public bool $modal;

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->keyword = '';
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
            'source_url' => $this->source_url,
            'language' => $this->language,
            'keyword' => $this->keyword,
            'tone' => $this->tone,
            'type' => DocumentType::BLOG_POST->value,
        ]);

        return redirect()->to('/pending');
    }
}
