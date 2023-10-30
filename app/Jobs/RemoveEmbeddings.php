<?php

namespace App\Jobs;

use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Packages\Oraculum\Oraculum;
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
            $oraculum = new Oraculum($user, $this->collectionName);
            $oraculum->deleteCollection();
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to delete collection: ' . $e->getMessage());
        }
    }
}
