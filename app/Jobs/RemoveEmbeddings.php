<?php

namespace App\Jobs;

use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected string $collectionName;
    protected array $meta;
    public OraculumFactoryInterface $oraculumFactory;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 7;

    /**
     * How many seconds Laravel should wait before retrying a job that has encountered an exception
     *
     * @var int
     */
    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [3, 7, 15];
    }

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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
    {
        $this->document = $document->fresh();
        $this->collectionName = $meta['collection_name'];
        $this->meta = $meta;
        $this->oraculumFactory = app(OraculumFactoryInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::findOrFail($this->document->getMeta('user_id'));
            $oraculum = $this->oraculumFactory->make($user, $this->collectionName);
            $oraculum->deleteCollection();
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailedButSkipped('Failed to delete collection: ' . $e->getMessage());
        }
    }
}
