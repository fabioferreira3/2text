<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Jobs\DispatchDocumentTasks;
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
        $this->repo->setDocument($document);
        $this->repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, [
            'order' => 1,
            'process_id' => $this->params['process_id']
        ]);

        $document->refresh();

        $platforms = collect($document->meta['platforms'])
            ->filter(function ($value, $key) {
                return $value;
            })
            ->keys();
        $platforms->each(function ($platform) use ($document) {
            CreateFromFreeText::dispatchIf(
                $this->params['source'] === 'free_text',
                $document,
                [...$this->params, 'platform' => $platform]
            );
            CreateFromWebsite::dispatchIf(
                $this->params['source'] === 'website_url',
                $document,
                [...$this->params, 'platform' => $platform]
            );
        });

        $this->repo->createTask(
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => 99
            ]
        );

        DispatchDocumentTasks::dispatch($document);
    }
}
