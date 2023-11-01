<?php

namespace App\Jobs\Blog;

use App\Models\Document;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateBlogPost
{
    use Dispatchable, SerializesModels;

    protected Document $document;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = [
            ...$params,
            'process_id' => Str::uuid()
        ];
    }

    public function handle()
    {
        CreateFromFreeText::dispatchIf($this->params['source'] === 'free_text', $this->document, $this->params);
        CreateFromVideoStream::dispatchIf($this->params['source'] === 'youtube', $this->document, $this->params);
        CreateFromWebsite::dispatchIf($this->params['source'] === 'website_url', $this->document, $this->params);
    }
}
