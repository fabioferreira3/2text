<?php

namespace App\Jobs\Blog;

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

class CreateBlogPostFromFreeText implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;
    public array $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $repo = new DocumentRepository($this->document);
        $repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, ['order' => 1, 'process_id' => $this->params['process_id']]);
        $repo->createTask(DocumentTaskEnum::CREATE_OUTLINE, [
            'process_id' => $this->params['process_id'],
            'meta' => [
                'target_headers_count' => $this->params['meta']['target_headers_count'],
                'keyword' => $this->params['meta']['keyword'],
                'tone' => $this->params['meta']['tone'],
            ],
            'order' => 2
        ]);
        $repo->createTask(DocumentTaskEnum::EXPAND_OUTLINE, [
            'process_id' => $this->params['process_id'],
            'meta' => [
                'tone' => $this->params['meta']['tone'],
            ],
            'tone' => $this->params['meta']['tone'], 'order' => 3
        ]);
        $repo->createTask(
            DocumentTaskEnum::EXPAND_TEXT,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'tone' => $this->params['meta']['tone'],
                ],
                'order' => 4
            ]
        );
        $repo->createTask(
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'keyword' => $this->params['meta']['keyword'],
                    'tone' => $this->params['meta']['tone'],
                ],
                'order' => 5
            ]
        );
        $repo->createTask(
            DocumentTaskEnum::CREATE_METADESCRIPTION,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'keyword' => $this->params['meta']['keyword'],
                    'tone' => $this->params['meta']['tone'],
                ],
                'order' => 6
            ]
        );

        DispatchDocumentTasks::dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_blog_post_from_free_text_' . $this->document->id;
    }
}
