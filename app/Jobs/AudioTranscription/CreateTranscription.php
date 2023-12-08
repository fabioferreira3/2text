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
    protected array $meta;
    public $processId;

    public function __construct(Document $document, array $meta)
    {
        $this->repo = new DocumentRepository();
        $this->document = $document;
        $this->meta = $meta;
        $this->processId = Str::uuid();
    }

    public function handle()
    {
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'source_url' => $this->document->getMeta('source_url')
                ],
                'order' => 1
            ]
        );

        if ($this->document->getMeta('identify_speakers')) {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION,
                [
                    'order' => 2,
                    'process_id' => $this->processId,
                    'meta' => [
                        'speakers_expected' => $this->document->getMeta('speakers_expected'),
                    ]
                ]
            );
        } else {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::TRANSCRIBE_AUDIO,
                [
                    'order' => 2,
                    'process_id' => $this->processId,
                    'meta' => []
                ]
            );
        }

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::PUBLISH_TEXT_BLOCK,
            [
                'order' => 3,
                'process_id' => $this->processId,
                'meta' => [
                    'text' => $this->document->getMeta('context') ?? null,
                    'target_language' => $this->document->getMeta('target_language') ?? null
                ]
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);
    }
}
