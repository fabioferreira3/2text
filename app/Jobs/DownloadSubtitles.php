<?php

namespace App\Jobs;

use App\Enums\DataType;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DownloadSubtitles implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $mediaRepo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->mediaRepo = new MediaRepository();
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $audioParams = $this->mediaRepo->downloadYoutubeSubtitles(
                $this->meta['source_url'],
                $this->meta['video_language'] ?? null
            );
            if (!$audioParams['subtitles']) {
                $audioParams = $this->mediaRepo->downloadYoutubeAudio($this->meta['source_url']);
            }

            if (($this->meta['embed_source'] ?? false) && $audioParams['subtitles']) {
                EmbedSource::dispatchSync($this->document, [
                    'data_type' => DataType::TEXT->value,
                    'source' => $audioParams['subtitles']
                ]);
            }

            // Update the document
            if (($this->meta['update_title']) ?? false) {
                $this->document->update(['title' => $audioParams['title']]);
            }

            $this->document->update(['meta' => [
                ...$this->document->meta,
                'context' => $audioParams['subtitles'] ?? null,
                'audio_file_path' => $audioParams['file_paths'],
                'duration' => $audioParams['total_duration']
            ]]);

            $this->jobSucceded();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->jobFailed('Audio download error: ' . $e->getMessage());
        }
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
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(2);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'download_audio_' . $this->document->id;
    }
}
