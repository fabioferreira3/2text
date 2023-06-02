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
        $repo->createTask(
            DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'keyword' => $this->document->meta['keyword'],
                    'tone' => $this->document->meta['tone'],
                    'platform' => $this->params['platform'],
                ],
                'order' => 2
            ]
        );

        DispatchDocumentTasks::dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_social_media_post_from_free_text_' . $this->params['platform'] . $this->document->id;
    }
}
