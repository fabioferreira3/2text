<?php

namespace App\Jobs\AudioTranscription;

use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Packages\AssemblyAI\AssemblyAI;
use App\Repositories\DocumentRepository;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostProcessAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    public array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
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
            DocumentRepository::updateTask($this->meta['pending_task_id'], 'finished');
            $transcription = MediaRepository::getTranscription($this->meta['transcript_id']);
            $subtitles = MediaRepository::getTranscriptionSubtitles($this->meta['transcript_id']);
            Log::debug($subtitles);
            $this->document->update([
                'meta' => [
                    ...$this->document->meta,
                    'context' => $transcription['text'],
                    'original_text' => $transcription['text'],
                    'transcript_id' => $this->meta['transcript_id'],
                    'vtt_file_path' => $subtitles['vtt_file_path'],
                    'srt_file_path' => $subtitles['srt_file_path']
                ]
            ]);
            $order = 1;
            foreach ($transcription['utterances'] as $utterance) {
                $this->document->contentBlocks()->save(new DocumentContentBlock([
                    'type' => 'text',
                    'content' => $utterance['text'],
                    'prefix' => 'Speaker ' . $utterance['speaker'],
                    'prompt' => null,
                    'order' => $order
                ]));
                $order++;
            }
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed($e);
        }
    }
}
