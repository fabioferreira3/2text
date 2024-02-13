<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Events\TitleGenerated;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\GenRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateTitle implements ShouldQueue, ShouldBeUnique
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
    public $tries = 5;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 5;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(2);
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
        $this->genRepo = app(GenRepository::class);
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
                $response = $this->genRepo->generateEmbeddedTitle($this->document, $this->meta['collection_name']);
            } else {
                $response = $this->genRepo->generateTitle(
                    $this->document,
                    $this->meta['text'] ?? $this->document->normalized_structure
                );
            }

            RegisterUnitsConsumption::dispatch($this->document->account, 'words_generation', [
                'word_count' => Str::wordCount($response['content']),
                'document_id' => $this->document->id,
                'job' => DocumentTaskEnum::CREATE_TITLE->value
            ]);

            RegisterAppUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::CREATE_TITLE->value
                ]
            ]);

            event(new TitleGenerated($this->document, $this->meta['process_id']));
            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to create title');
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        $id = $this->meta['process_id'] ?? $this->document->id;
        return 'create_title_' . $id;
    }
}
