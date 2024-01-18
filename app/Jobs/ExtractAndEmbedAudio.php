<?php

namespace App\Jobs;

use App\Enums\DataType;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExtractAndEmbedAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $mediaRepo;

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
        return now()->addMinutes(10);
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
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new ThrottlesExceptions(10, 5)];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->mediaRepo = app(MediaRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $audioParams = $this->mediaRepo->downloadYoutubeAudio($this->meta['source_url']);
            $transcribedText = $this->mediaRepo->transcribeAudio($audioParams['file_paths']);
            $finalTranscription = "Title: " . $audioParams['title'] . "Content: " . $transcribedText;
            EmbedSource::dispatchSync($this->document, [
                'data_type' => DataType::TEXT->value,
                'source' => $finalTranscription,
                'collection_name' => $this->meta['collection_name']
            ]);
            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Audio extraction and embedding error');
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'extract_audio_' . $this->document->id;
    }
}
