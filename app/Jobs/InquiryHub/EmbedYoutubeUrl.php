<?php

namespace App\Jobs\InquiryHub;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class EmbedYoutubeUrl
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
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EMBED_SOURCE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'data_type' => 'text',
                    'source' => $this->params['source'],
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);
    }
}
