<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Jobs\RegisterUnitsConsumption;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishTextBlocks implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document;
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $repo = new DocumentRepository($this->document);
            $repo->publishContentBlocks();

            RegisterUnitsConsumption::dispatch($this->document->account, 'words_generation', [
                'word_count' => $this->document->refresh()->word_count,
                'document_id' => $this->document->id,
                'job' => DocumentTaskEnum::PUBLISH_TEXT_BLOCK->value
            ]);

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to publish text blocks: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'publish_text_blocks_' . $this->document->id;
    }
}
