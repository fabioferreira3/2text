<?php

namespace App\Jobs\InquiryHub;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegisterEmbedWebsite
{
    use Dispatchable, SerializesModels;

    protected Document $document;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
    }

    public function handle()
    {
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EMBED_SOURCE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'data_type' => DataType::WEB_PAGE->value,
                    'source' => $this->params['source'],
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );
    }
}
