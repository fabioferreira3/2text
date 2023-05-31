<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentType;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateSocialMediaPost
{
    use Dispatchable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = [
            ...$params,
            'process_id' => Str::uuid(),
            'type' => DocumentType::SOCIAL_MEDIA_POST->value
        ];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->createSocialMediaPost($this->params);
        $platforms = collect($document->meta['platforms'])
            ->filter(function ($value, $key) {
                return $value;
            })
            ->keys()
            ->toArray();;
        CreateFromFreeText::dispatchIf($this->params['source'] === 'free_text', $document, $this->params);
        //CreateBlogPostFromVideoStream::dispatchIf($this->params['source'] === 'youtube', $document, $this->params);
    }
}
