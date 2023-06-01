<?php

namespace App\Jobs\SocialMedia;

use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePost implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = new PromptHelper($document->language->value);
        $this->repo = new DocumentRepository($this->document);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $chatGpt = new ChatGPT();
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>   $this->promptHelper->writeSocialMediaPost([
                        'context' => $this->document->meta['summary'] ?? $this->document->meta['context'],
                        'keyword' => $this->document->meta['keyword'] ?? null,
                        'platform' => $this->meta['platform'],
                        'tone' => $this->document->meta['tone'],
                        'more_instructions' => $this->document->meta['more_instructions'] ?? null
                    ])
                ]
            ]);
            $this->repo->updateMeta($this->meta['platform'], $response['content']);
            $this->repo->addHistory(
                [
                    'field' => $this->meta['platform'],
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to generate ' . $this->meta['platform'] . ' post: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_social_media_post_' . $this->meta['platform'] . $this->meta['process_id'] ?? $this->document->id;
    }
}
