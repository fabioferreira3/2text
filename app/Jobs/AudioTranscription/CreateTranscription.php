<?php

namespace App\Jobs\AudioTranscription;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateTranscription
{
    use Dispatchable, SerializesModels;

    protected Document $document;
    protected $repo;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->repo = new DocumentRepository();
        $this->document = $document;
        $this->params = $params;
    }

    public function handle()
    {
        $processId = Str::uuid();
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $processId,
                'meta' => [
                    'source_url' => $this->document->meta['source_url']
                ],
                'order' => 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::PROCESS_AUDIO,
            [
                'order' => 2,
                'process_id' => $processId,
            ]
        );

        if ($this->params['target_language'] !== 'same') {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::PREPARE_TEXT_TRANSLATION,
                [
                    'order' => 3,
                    'process_id' => $processId,
                    'meta' => [
                        'process_id' => $processId,
                        'target_language' => $this->params['target_language'],
                    ]
                ]
            );
        }

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::PUBLISH_TRANSCRIPTION,
            [
                'order' => 1000,
                'process_id' => $processId
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);
    }
}
