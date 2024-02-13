<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Jobs\Traits\JobEndings;
use App\Jobs\Translation\TranslateTextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\GenRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SummarizeContent implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

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
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10, 15];
    }

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
            $genRepo = App::make(GenRepository::class);
            if ($this->meta['query_embedding'] ?? false) {
                $response = $genRepo->generateEmbeddedSummary($this->document, $this->meta);
            } else {
                $this->meta['content'] = $this->meta['content'] ?? $this->document->getMeta('context');
                $response = $genRepo->generateSummary($this->document, $this->meta);
            }

            $contentBlock = $this->document->contentBlocks()->save((new DocumentContentBlock([
                'type' => 'text',
                'content' => $response['content'],
                'prompt' => null,
                'order' => 1
            ])));

            RegisterUnitsConsumption::dispatch($this->document->account, 'words_generation', [
                'word_count' => Str::wordCount($response['content']),
                'document_id' => $this->document->id,
                'job' => DocumentTaskEnum::SUMMARIZE_CONTENT->value
            ]);

            RegisterAppUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::SUMMARIZE_CONTENT->value
                ]
            ]);

            if ($this->document->getMeta('target_language')) {
                TranslateTextBlock::dispatchSync($this->document, [
                    'content_block_id' => $contentBlock->id,
                    'target_language' => $this->document->getMeta('target_language')
                ]);
            }

            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to summarize content');
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        $id = $this->meta['process_id'] ?? $this->document->id;
        return 'summarizing_content_' . $id;
    }
}
