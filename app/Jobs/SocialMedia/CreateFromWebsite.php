<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class CreateFromWebsite implements ShouldQueue, ShouldBeUnique
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
        Artisan::call('crawl', ['url' => $this->params['meta']['source_url']]);
        $websiteContent = Artisan::output();

        $this->document->update([
            'meta' => [
                ...$this->document->meta,
                'context' => $websiteContent,
                'original_text' => $websiteContent
            ]
        ]);

        $repo = new DocumentRepository($this->document);
        $repo->createTask(
            DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'platform' => $this->params['platform'],
                ],
                'order' => 2
            ]
        );
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_social_media_post_from_free_text_' . $this->params['platform'] . $this->document->id;
    }
}
