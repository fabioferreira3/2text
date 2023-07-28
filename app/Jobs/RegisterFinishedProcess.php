<?php

namespace App\Jobs;

use App\Events\ProcessFinished;
use App\Jobs\Contact\NotifyFinished;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class RegisterFinishedProcess implements ShouldQueue
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
            ProcessFinished::dispatch([
                'document_id' => $this->document->id,
                'process_id' => $this->meta['process_id'],
                'user_id' => $this->document['meta']['user_id']
            ]);
            NotifyFinished::dispatch($this->document, $this->document['meta']['user_id']);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to register finished process: ' . $e->getMessage());
        }
    }
}
