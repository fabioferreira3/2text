<?php

namespace App\Jobs\Blog;

use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateBlogPost implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = [...$params, 'process_id' => Str::uuid()];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->create(['type' => $this->params['type']]);
        CreateBlogPostFromUrl::dispatchIf($this->params['source'] === 'youtube', $document, $this->params);
        //ProcessRequestFromText::dispatchIf($this->textRequest->source_provider === 'free_text', $this->textRequest);
    }
}
