<?php

namespace App\Jobs;

use App\Enums\LanguageModels;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SummarizeDocument implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = new PromptHelper($document->language->value);
        $this->repo = new DocumentRepository($this->document);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (isset($this->document->meta['context']) && Str::wordCount($this->document->meta['context'] < 2000)) {
                $this->jobSkipped();
                return;
            }

            $chatGpt = new ChatGPT(LanguageModels::GPT_3_TURBO->value);

            $sentences = collect(preg_split("/(?<=[.?!])\s+(?=([^\d\w]*[A-Z][^.?!]+))/", $this->document->meta['context'], -1, PREG_SPLIT_NO_EMPTY));
            $paragraphs = collect([]);

            $sentences->chunk(12)->each(function ($chunk) use ($paragraphs) {
                $paragraphs->push($chunk);
            });

            $paragraphs = $paragraphs->map(function ($paragraph) {
                return $paragraph->join(' ');
            });

            $rewrittenParagraphs = collect([]);
            $messages = collect([]);

            // Paragraphs generation
            $paragraphs->each(function ($paragraph) use (&$messages, &$rewrittenParagraphs, &$chatGpt) {
                $allContent = $messages->map(function ($message) {
                    return $message['content'];
                })->join("");
                $tokenCount = $chatGpt->countTokens($allContent);
                $assistantContent = $messages->filter(function ($message) {
                    return $message['role'] === 'assistant';
                })->map(function ($message) {
                    return $message['content'];
                })->join("");

                if ($tokenCount > 2000) {
                    $messages = collect([]);
                    $rewrittenParagraphs = collect([]);
                    $response = $chatGpt->request([[
                        'role' => 'user',
                        'content' => $this->promptHelper->summarize($assistantContent)
                    ]]);
                } else {
                    $response = $chatGpt->request([[
                        'role' => 'user',
                        'content' => $this->promptHelper->simplify($paragraph)
                    ]]);
                }

                $rewrittenParagraphs->push($response['content']);
                $messages->push([
                    'role' => 'assistant',
                    'content' => $response['content']
                ]);
                $this->repo->addHistory(
                    [
                        'field' => 'partial_summary',
                        'content' => $response['content']
                    ],
                    $response['token_usage']
                );
            });
            $allRewrittenParagraphs = $rewrittenParagraphs->join(' ');
            $this->document->update(['meta' => [...$this->document['meta'], 'summary' => $allRewrittenParagraphs !== "" ? $allRewrittenParagraphs : null]]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to generate summary: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_summary_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
