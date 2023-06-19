<?php

namespace App\Jobs;

use App\Helpers\DocumentHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class CrawlWebsite implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;

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
        function unicodeToPlainText($text)
        {
            $decodedText = json_decode(sprintf('"%s"', $text));

            // Check if json_decode fails, which means the text probably didn't have unicode sequences
            if ($decodedText === null) {
                return $text;
            }

            return $decodedText;
        }
        try {
            Artisan::call('crawl', ['url' => $this->document['meta']['source_url']]);
            $websiteContent = Artisan::output();

            $this->document->update([
                'meta' => [
                    ...$this->document->meta,
                    'context' => $websiteContent,
                    'original_text' => $websiteContent,
                    'original_sentences' => ($this->meta['parse_sentences'] ?? false) ? DocumentHelper::breakTextIntoSentences(unicodeToPlainText($websiteContent)) : null
                ]
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create title: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'crawl_website_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
