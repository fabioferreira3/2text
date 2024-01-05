<?php

namespace App\Jobs\InsightHub;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterEmbedFile
{
    use Dispatchable, SerializesModels;

    public Document $document;
    public array $params;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
    }

    public function handle()
    {
        $dataType = DataType::tryFrom($this->params['source_type']);
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EMBED_SOURCE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'data_type' => $dataType->value,
                    'source' => $this->document->getMeta('source_file_path'),
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );
    }
}
