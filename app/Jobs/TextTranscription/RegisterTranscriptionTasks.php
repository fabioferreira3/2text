<?php

namespace App\Jobs\TextTranscription;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterTranscriptionTasks
{
    use Dispatchable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->params = $params;
        $this->repo = new DocumentRepository($document);
    }

    public function handle()
    {
        $this->repo->createTask(
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'source_url' => $this->params['meta']['source_url']
                ],
                'order' => 1
            ]
        );
        $this->repo->createTask(DocumentTaskEnum::PROCESS_AUDIO, [
            'process_id' => $this->params['process_id'],
            'order' => 2
        ]);
    }
}
