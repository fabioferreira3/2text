<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateSocialMediaPost
{
    use Dispatchable, SerializesModels;

    protected $document;
    protected $repo;
    protected string $processId;

    public function __construct($document)
    {
        $this->document = $document;
        $this->processId = Str::uuid();
        $this->repo = new DocumentRepository();
        $this->repo->setDocument($document);
    }

    public function handle()
    {
        $this->repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, [
            'order' => 1,
            'process_id' => $this->processId
        ]);

        $this->document->refresh();

        if ($this->document->meta['source'] === 'website_url') {
            $this->repo->createTask(
                DocumentTaskEnum::CRAWL_WEBSITE,
                [
                    'process_id' => $this->processId,
                    'meta' => [],
                    'order' => 2
                ]
            );
        } elseif ($this->document->meta['source'] === 'youtube') {
            $this->repo->createTask(
                DocumentTaskEnum::DOWNLOAD_AUDIO,
                [
                    'process_id' => $this->processId,
                    'meta' => [
                        'source_url' => $this->document->meta['source_url']
                    ],
                    'order' => 2
                ]
            );
            $this->repo->createTask(
                DocumentTaskEnum::PROCESS_AUDIO,
                [
                    'process_id' => $this->processId,
                    'meta' => [],
                    'order' => 3
                ]
            );
        }

        $platforms = collect($this->document->meta['platforms'])
            ->filter(function ($value) {
                return $value;
            })->keys();
        $platforms->each(function ($platform, $index) {
            $this->repo->createTask(
                DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
                [
                    'process_id' => $this->processId,
                    'meta' => [
                        'platform' => $platform,
                    ],
                    'order' => 3 + $index
                ]
            );
        });

        $this->repo->createTask(
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->processId,
                'meta' => [],
                'order' => 99
            ]
        );

        $this->repo->createTask(DocumentTaskEnum::REGISTER_FINISHED_PROCESS, [
            'order' => 100,
            'process_id' => $this->processId,
            'meta' => []
        ]);

        DispatchDocumentTasks::dispatch($this->document);
    }
}
