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

    public function __construct(Document $document, array $meta)
    {
        $this->repo = new DocumentRepository();
        $this->document = $document;
        $this->meta = $meta;
    }

    public function handle()
    {
        $processId = Str::uuid();
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $processId,
                'meta' => [
                    'source_url' => $this->document->getMeta('source_url')
                ],
                'order' => 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::TRANSCRIBE_AUDIO,
            [
                'order' => 2,
                'process_id' => $processId,
                'meta' => [
                    'identify_speakers' => $this->document->getMeta('identify_speakers'),
                    'speakers_expected' => $this->document->getMeta('speakers_expected'),
                    'target_language' => $this->document->getMeta('target_language')
                ]
            ]
        );

        if ($this->meta[]) {
        }

        DispatchDocumentTasks::dispatch($this->document);
    }
}
