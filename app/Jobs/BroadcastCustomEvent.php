<?php

namespace App\Jobs;

use App\Events\BroadcastEvent;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastCustomEvent implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, $meta = [])
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
            event(new BroadcastEvent([
                'user_id' => $this->document->getMeta('user_id'),
                'event_name' => $this->meta['event_name'],
                'payload' => [
                    'document_id' => $this->document->id,
                ]
            ]));

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Error broadcasting custom event: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'broadcast_custom_event_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
