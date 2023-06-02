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

class CreateFromVideoStream implements ShouldQueue, ShouldBeUnique
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
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'source_url' => $this->document->meta['source_url']
                ],
                'order' => 1
            ]
        );
        $repo->createTask(DocumentTaskEnum::PROCESS_AUDIO, [
            'process_id' => $this->params['process_id'],
            'order' => 2
        ]);
        RegisterCreationTasks::dispatchSync($this->document, [
            ...$this->params,
            'next_order' => 3
        ]);

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
