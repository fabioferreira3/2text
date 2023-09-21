<?php

namespace App\Jobs;

use App\Events\ContentBlockUpdated;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\GenRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RewriteTextBlock implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
    {
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
            $contentBlock = DocumentContentBlock::findOrFail($this->meta['document_content_block_id']);
            GenRepository::rewriteTextBlock($contentBlock, $this->meta);
            event(new ContentBlockUpdated($contentBlock, $this->meta['process_id']));
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to rewrite text block: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'rewrite_text_block_' . $this->meta['process_id'] ?? $this->contentBlock->id;
    }
}
