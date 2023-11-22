<?php

namespace App\Jobs\InquiryHub;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterEmbedVideoStream
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
            DocumentTaskEnum::DOWNLOAD_SUBTITLES,
            [
                'order' => 1,
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'source_url' => $this->params['source_url'] ?? $this->document->getMeta('source_url'),
                    'embed_source' => true
                ],
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::TRANSCRIBE_AUDIO,
            [
                'order' => 2,
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'abort_when_context_present' => true,
                    'embed_source' => true
                ]
            ]
        );
    }
}
