<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentType;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateBlogPost
{
    use Dispatchable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = [
            ...$params,
            'process_id' => Str::uuid(),
            'type' => DocumentType::BLOG_POST->value
        ];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->createBlogPost($this->params);
        CreateBlogPostFromVideoStream::dispatchIf($this->params['source'] === 'youtube', $document, $this->params);
        CreateBlogPostFromFreeText::dispatchIf($this->params['source'] === 'free_text', $document, $this->params);
        CreateBlogPostFromWebsite::dispatchIf($this->params['source'] === 'website_url', $document, $this->params);
    }
}
