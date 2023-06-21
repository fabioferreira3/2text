<?php

namespace App\Jobs\TextTranscription;

use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PublishTranscription implements ShouldQueue, ShouldBeUnique
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
            $meta = $this->document->meta;
            $this->document->update([
                'content' => $meta['translated_text'] ?? $meta['original_text'],
                'word_count' => Str::wordCount($meta['translated_text'] ?? $meta['original_text']),
                'meta' => [
                    ...$meta,
                    'source' => $meta['source'],
                    'source_url' => $meta['source_url'],
                    'duration' => $meta['duration'],
                    'audio_file_path' => $meta['audio_file_path'],
                ]
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed($e);
        }
    }
}
