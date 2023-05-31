<?php

namespace App\Jobs\SocialMedia;

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

class CreateFromFreeText implements ShouldQueue, ShouldBeUnique
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
        $repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, [
            'order' => 1,
            'process_id' => $this->params['process_id']
        ]);
        $repo->createTask(
            DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'keyword' => $this->params['meta']['keyword'],
                    'tone' => $this->params['meta']['tone'],
                    'platform' => $this->params['meta']['platform'],
                    'more_instructions' => $this->params['meta']['more_instructions']
                ],
                'order' => 2
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
                'order' => 3
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
