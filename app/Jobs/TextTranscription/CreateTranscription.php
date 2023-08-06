<?php

namespace App\Jobs\TextTranscription;

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
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
        $this->repo->setDocument($this->document);
        $this->repo->createTask(
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $processId,
                'meta' => [
                    'source_url' => $this->document->meta['source_url']
                ],
                'order' => 1
            ]
        );
        $this->repo->createTask(DocumentTaskEnum::PROCESS_AUDIO, [
            'order' => 2,
            'process_id' => $processId,
        ]);
        if ($this->params['target_language'] !== 'same') {
            $this->repo->createTask(DocumentTaskEnum::TRANSLATE_TEXT, [
                'order' => 3,
                'process_id' => $processId, 'meta' => [
                    'target_language' => Language::from($this->params['target_language'])->name
                ]
            ]);
        }
        $this->repo->createTask(DocumentTaskEnum::PUBLISH_TRANSCRIPTION, [
            'order' => 4,
            'process_id' => $processId
        ]);
        DispatchDocumentTasks::dispatch($this->document);
    }
}
