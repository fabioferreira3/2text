<?php

namespace App\Jobs\AudioTranscription;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @codeCoverageIgnore
 */
class RegisterTranscriptionTasks
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
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'source_url' => $this->params['meta']['source_url']
                ],
                'order' => 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::PROCESS_AUDIO,
            [
                'process_id' => $this->params['process_id'],
                'order' => 2
            ]
        );
    }
}
