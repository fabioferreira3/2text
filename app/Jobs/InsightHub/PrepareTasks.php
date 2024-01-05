<?php

namespace App\Jobs\InsightHub;

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PrepareTasks
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
        RegisterEmbedFreeText::dispatchIf(
            $this->params['source_type'] === SourceProvider::FREE_TEXT->value,
            $this->document,
            $this->params
        );

        RegisterEmbedVideoStream::dispatchIf(
            $this->params['source_type'] === SourceProvider::YOUTUBE->value,
            $this->document,
            $this->params
        );

        RegisterEmbedWebsite::dispatchIf(
            $this->params['source_type'] === SourceProvider::WEBSITE_URL->value,
            $this->document,
            [...$this->params, 'source' => $this->params['source_url']]
        );

        RegisterEmbedFile::dispatchIf(
            in_array($this->params['source_type'], [
                SourceProvider::PDF->value,
                SourceProvider::DOCX->value,
                SourceProvider::CSV->value,
            ]),
            $this->document,
            [...$this->params, 'source' => $this->params['source_url']]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::BROADCAST_CUSTOM_EVENT,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'event_name' => 'EmbedCompleted'
                ],
                'order' => 999
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);
    }
}
