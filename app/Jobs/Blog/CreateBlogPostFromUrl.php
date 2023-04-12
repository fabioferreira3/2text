<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class CreateBlogPostFromUrl implements ShouldQueue, ShouldBeUnique
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
        $repo = new DocumentRepository();
        $repo->createTask($this->document, DocumentTaskEnum::DOWNLOAD_AUDIO, [...$this->params, 'status' => 'in_progress']);
        $repo->createTask($this->document, DocumentTaskEnum::PROCESS_AUDIO, $this->params);
        // Bus::chain([
        //     new DownloadAudio($textRequest),
        //     new ProcessAudio($textRequest),
        //     new BloggifyText($textRequest),
        //     new FinalizeProcess($textRequest)
        // ])->dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_blog_post_from_url_' . $this->document->id;
    }
}
