<?php

namespace App\Jobs\Blog;

use App\Events\MetaDescriptionGenerated;
use App\Jobs\RegisterProductUsage;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\GenRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Exception;

class CreateMetaDescription implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $genRepo;

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
        $this->genRepo = new GenRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = $this->genRepo->generateMetaDescription($this->document);
            $this->document->contentBlocks()->save(new DocumentContentBlock([
                'type' => 'meta_description',
                'content' => Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"'),
                'prompt' => '',
                'order' => 1
            ]));
            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);
            event(new MetaDescriptionGenerated($this->document, $this->meta['process_id']));
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->handleError($e, 'Failed to create meta description');
        }
    }
}
