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

class CreateFromFreeText implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;
    public array $params;
    public $processId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
        $this->processId = $params['process_id'] ?? Str::uuid();
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
            DocumentTaskEnum::SUMMARIZE_CONTENT,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'content' => $this->document->content ?? null,
                    'query_embedding' => false,
                    'max_words_count' => $this->document->getMeta('max_words_count')
                ],
                'order' => 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::BROADCAST_CUSTOM_EVENT,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'event_name' => 'SummaryCompleted'
                ],
                'order' => 2
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_summary_from_free_text_' . $this->document->id;
    }
}
