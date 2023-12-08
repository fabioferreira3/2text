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
use Illuminate\Queue\SerializesModels;

class TranscribeAudio implements ShouldQueue, ShouldBeUnique
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (($this->meta['abort_when_context_present'] ?? false) && $this->document->getMeta('context')) {
                $this->jobSkipped();
                return;
            }

            $transcribedText = $this->mediaRepo->transcribeAudio(
                $this->document->getMeta('audio_file_path')
            );

            if ($this->meta['embed_source'] ?? false) {
                EmbedSource::dispatchSync($this->document, [
                    'data_type' => DataType::TEXT->value,
                    'source' => $transcribedText
                ]);
            }

            $this->document->update([
                'meta' => [
                    ...$this->document->meta,
                    'context' => $transcribedText,
                    'original_text' => $transcribedText
                ]
            ]);

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Transcribing audio error: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'transcribing_audio_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
