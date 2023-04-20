<?php

namespace App\Jobs\Blog;

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
        $this->params = [...$params, 'process_id' => Str::uuid()];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->create($this->params);
        CreateBlogPostFromVideoStream::dispatchIf($this->params['source'] === 'youtube', $document, $this->params);
        CreateBlogPostFromFreeText::dispatchIf($this->params['source'] === 'free_text', $document, $this->params);
    }
}
