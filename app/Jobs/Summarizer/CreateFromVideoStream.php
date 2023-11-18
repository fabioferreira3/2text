<?php

namespace App\Jobs\Summarizer;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateFromVideoStream implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;
    public array $params;
    public string $processId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->processId = $params['process_id'] ?? Str::uuid();
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_SUBTITLES,
            [
                'process_id' => $this->processId,
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
                'process_id' => $this->processId,
                'order' => 2,
                'meta' => [
                    'abort_when_context_present' => true
                ]
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::SUMMARIZE_CONTENT,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'content' => null,
                    'query_embedding' => false,
                    'max_words_count' => $this->document->getMeta('max_words_count')
                ],
                'order' => 4
            ]
        );
        DispatchDocumentTasks::dispatch($this->document);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_summary_from_video_stream_' . $this->document->id;
    }
}
