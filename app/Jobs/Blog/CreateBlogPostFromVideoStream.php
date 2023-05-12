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

class CreateBlogPostFromVideoStream implements ShouldQueue, ShouldBeUnique
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
        $repo->createTask(
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'meta' => [
                    'source_url' => $this->params['meta']['source_url']
                ],
                'order' => 1
            ]
        );
        $repo->createTask(DocumentTaskEnum::PROCESS_AUDIO, ['order' => 2]);
        $repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, ['order' => 3]);
        $repo->createTask(DocumentTaskEnum::CREATE_OUTLINE, [
            'meta' => [
                'target_headers_count' => $this->params['meta']['target_headers_count'],
                'keyword' => $this->params['meta']['keyword'],
                'tone' => $this->params['meta']['tone'],
            ],
            'order' => 4
        ]);
        $repo->createTask(DocumentTaskEnum::EXPAND_OUTLINE, [
            'meta' => [
                'keyword' => $this->params['meta']['keyword'],
                'tone' => $this->params['meta']['tone']
            ],
            'order' => 5
        ]);
        $repo->createTask(
            DocumentTaskEnum::EXPAND_TEXT,
            [
                'meta' => [
                    'tone' => $this->params['meta']['tone']
                ],
                'order' => 6
            ]
        );
        $repo->createTask(
            DocumentTaskEnum::CREATE_TITLE,
            [
                'meta' => [
                    'keyword' => $this->params['meta']['keyword'],
                    'tone' => $this->params['meta']['tone'],
                ],
                'order' => 7
            ]
        );
        $repo->createTask(
            DocumentTaskEnum::CREATE_METADESCRIPTION,
            [
                'meta' => [
                    'keyword' => $this->params['meta']['keyword'],
                    'tone' => $this->params['meta']['tone'],
                ],
                'order' => 8
            ]
        );

        DispatchDocumentTasks::dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_blog_post_from_video_stream_' . $this->document->id;
    }
}
