<?php

namespace App\Jobs;

use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class RegisterContentHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
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
            $this->repo->addHistory([
                'field' => 'content',
                'content' => $this->document->normalized_structure,
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand text section: ' . $e->getMessage());
        }
    }
}
