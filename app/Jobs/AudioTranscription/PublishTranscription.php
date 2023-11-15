<?php

namespace App\Jobs\AudioTranscription;

use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
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
            $this->document->contentBlocks()->save(new DocumentContentBlock([
                'type' => 'text',
                'content' => $meta['translated_text'] ?? $meta['original_text'],
                'prompt' => null,
                'order' => 1
            ]));
            $this->document->update([
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
