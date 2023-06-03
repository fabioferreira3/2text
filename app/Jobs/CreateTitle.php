<?php

namespace App\Jobs;

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

class CreateTitle implements ShouldQueue, ShouldBeUnique
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
            GenRepository::generateTitle($this->document);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create title: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_title_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
