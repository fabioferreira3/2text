<?php

namespace App\Jobs\InsightHub;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterEmbedVideoStream
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
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_SUBTITLES,
            [
                'order' => 1,
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'source_url' => $this->params['source_url'] ?? $this->document->getMeta('source_url'),
                    'video_language' => $this->params['video_language'],
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
