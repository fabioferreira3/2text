<?php

namespace App\Jobs\Blog;

use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
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
        RegisterCreationTasks::dispatchSync($this->document, [
            ...$this->params,
            'next_order' => 1
        ]);
        DispatchDocumentTasks::dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_blog_post_from_website_url_' . $this->document->id;
    }
}
