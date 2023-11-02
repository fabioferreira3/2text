<?php

namespace App\Jobs\Blog;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
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
        $this->processId = Str::uuid();
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->defineTasksCount();
        $queryEmbedding = true;
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::REMOVE_EMBEDDINGS,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );

        $nextOrder = 2;

        if ($this->document->getMeta('source') === SourceProvider::WEBSITE_URL->value) {
            foreach ($this->document->getMeta('source_urls') as $key => $sourceUrl) {
                DocumentRepository::createTask(
                    $this->document->id,
                    DocumentTaskEnum::EMBED_SOURCE,
                    [
                        'process_id' => $this->processId,
                        'meta' => [
                            'data_type' => DataType::WEB_PAGE->value,
                            'source' => $sourceUrl,
                            'collection_name' => $this->document->id
                        ],
                        'order' => $nextOrder
                    ]
                );
                $nextOrder += 1;
            }
        }

        RegisterCreationTasks::dispatchSync($this->document, [
            ...$this->params,
            'next_order' => $nextOrder,
            'process_id' => $this->processId,
            'query_embedding' => $queryEmbedding,
            'collection_name' => $this->document->id
        ]);

        DispatchDocumentTasks::dispatch($this->document);
    }

    public function defineTasksCount()
    {
        $tasksCount = 9 + $this->document->getMeta('target_headers_count');
        if ($this->document->getMeta('source') === SourceProvider::WEBSITE_URL->value) {
            $tasksCount += count($this->document->getMeta('source_urls'));
        }

        if ($this->document->getMeta('generate_image') ?? false) {
            $tasksCount += 1;
        }

        $repo = new DocumentRepository(($this->document));
        $repo->updateMeta('total_tasks_count', $tasksCount);
        $repo->updateMeta('completed_tasks_count', 0);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_blog_post_from_website_url_' . $this->document->id;
    }
}
