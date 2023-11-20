<?php

namespace App\Jobs\Summarizer;

use App\Enums\DataType;
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

class CreateFromWebsite implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;
    public string $processId;
    public array $params;

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
            DocumentTaskEnum::EMBED_SOURCE,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'data_type' => DataType::WEB_PAGE->value,
                    'source' => $this->document->getMeta('source_url'),
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );

        // DocumentRepository::createTask(
        //     $this->document->id,
        //     DocumentTaskEnum::SUMMARIZE_CONTENT,
        //     [
        //         'process_id' => $this->processId,
        //         'meta' => [
        //             'content' => null,
        //             'collection_name' => $this->document->id,
        //             'query_embedding' => true,
        //             'max_words_count' => $this->document->getMeta('max_words_count')
        //         ],
        //         'order' => 2
        //     ]
        // );

        // DocumentRepository::createTask(
        //     $this->document->id,
        //     DocumentTaskEnum::BROADCAST_CUSTOM_EVENT,
        //     [
        //         'process_id' => $this->params['process_id'] ?? Str::uuid(),
        //         'meta' => [
        //             'event_name' => 'SummaryCompleted'
        //         ],
        //         'order' => 3
        //     ]
        // );

        DispatchDocumentTasks::dispatch($this->document);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'creating_summary_from_website_url_' . $this->document->id;
    }
}
