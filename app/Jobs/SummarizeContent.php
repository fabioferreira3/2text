<?php

namespace App\Jobs;

use App\Jobs\Traits\JobEndings;
use App\Jobs\Translation\TranslateTextBlock;
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

class SummarizeContent implements ShouldQueue, ShouldBeUnique
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
        $this->document = $document->fresh();
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
            if ($this->meta['query_embedding'] ?? false) {
                $response = GenRepository::generateEmbeddedSummary($this->document, $this->meta);
            } else {
                $this->meta['content'] = $this->meta['content'] ?? $this->document->getMeta('context');
                $response = GenRepository::generateSummary($this->document, $this->meta);
            }

            $contentBlock = $this->document->contentBlocks()->save((new DocumentContentBlock([
                'type' => 'text',
                'content' => $response['content'],
                'prompt' => null,
                'order' => 1
            ])));

            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);

            if ($this->document->getMeta('target_language')) {
                TranslateTextBlock::dispatchSync($this->document, [
                    'content_block_id' => $contentBlock->id,
                    'target_language' => $this->document->getMeta('target_language')
                ]);
            }

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to summarize content: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'summarizing_content_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
