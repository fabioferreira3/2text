<?php

namespace App\Jobs;

use App\Events\BroadcastEvent;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
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
            if ($this->meta['query_embedded'] ?? false) {
            } else {
                GenRepository::generateSummary($this->document, $this->meta);
            }
            event(new BroadcastEvent([
                'user_id' => $this->document->getMeta('user_id'),
                'event_name' => 'SummaryCompleted',
                'payload' => [
                    'document_id' => $this->document->id,
                ]
            ]));

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
