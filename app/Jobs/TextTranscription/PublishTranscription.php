<?php

namespace App\Jobs\TextTranscription;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PublishTranscription implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Document $document;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document)
    {
        $this->document = $document->fresh();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $meta = $this->document->meta;
        $this->document->update([
            'content' => $this->document->meta['original_text'],
            'word_count' => Str::wordCount($this->document->meta['original_text']),
            'meta' => [
                'source' => $meta['source'],
                'source_url' => $meta['source_url']
            ]
        ]);
    }
}
