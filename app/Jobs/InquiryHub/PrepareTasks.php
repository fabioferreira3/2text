<?php

namespace App\Jobs\InquiryHub;

use App\Enums\DocumentTaskEnum;
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
            $this->params['source_type'] === 'free_text',
            $this->document,
            $this->params
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
