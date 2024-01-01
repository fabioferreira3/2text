<?php

namespace App\Jobs;

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

class TranscribeAudioWithDiarization implements ShouldQueue, ShouldBeUnique
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
            $params = [
                'document_id' => $this->document->id,
                'language' => $this->document->language,
                'task_id' => $this->meta['task_id']
            ];
            if ($this->document->getMeta('speakers_expected')) {
                $params['speakers_expected'] = (int) $this->document->getMeta('speakers_expected');
            }
            $this->mediaRepo->transcribeAudioWithDiarization(
                $this->document->getMeta('audio_file_path'),
                $params
            );
            $this->jobPending();
        } catch (Exception $e) {
            $this->jobFailed('Transcribing audio with diarization error: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'transcribing_audio_with_diarization_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
