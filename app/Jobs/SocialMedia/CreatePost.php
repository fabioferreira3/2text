<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Helpers\PromptHelper;
use App\Jobs\RegisterProductUsage;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Livewire\WithFileUploads;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreatePost implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings, WithFileUploads;

    public Document $document;
    public array $meta;
    public PromptHelper $promptHelper;
    public DocumentRepository $repo;
    public $generator;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [], GenRepository $generator = null)
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = new PromptHelper($document->language->value);
        $this->repo = new DocumentRepository($this->document);
        $this->generator = $generator ?? app(GenRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->meta['query_embedding'] ?? false) {
                $response = $this->generator->generateEmbeddedSocialMediaPost(
                    $this->document,
                    $this->meta['platform'],
                    $this->meta['collection_name']
                );
            } else {
                $response = $this->generator->generateSocialMediaPost($this->document, $this->meta['platform']);
            }

            $this->document->contentBlocks()->save(
                new DocumentContentBlock([
                    'type' => 'text',
                    'content' => $response['content']
                ])
            );

            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->value
                ]
            ]);

            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to generate ' . $this->meta['platform'] . ' post');
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
