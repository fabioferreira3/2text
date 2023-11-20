<?php

namespace App\Jobs;

use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Packages\OpenAI\ChatGPT;
use App\Packages\Oraculum\Oraculum;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpandTextSection implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;

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
        return now()->addMinutes(5);
    }

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
            $rawStructure = $this->document->meta['raw_structure'];
            $normalizedStructure = $this->document->normalized_structure;
            $basePrompt = $this->promptHelper->givenFollowingText($normalizedStructure);

            if ($this->meta['query_embedding'] ?? false) {
                $response = $this->queryEmbedding($normalizedStructure, $basePrompt);
            } else {
                $response = $this->queryGpt($normalizedStructure, $basePrompt);
            }

            $rawStructure[$this->meta['section_key']]['content'] = $response['content'];
            $this->repo->updateMeta('raw_structure', $rawStructure);
            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand text section: ' . $e->getMessage());
        }
    }

    protected function queryEmbedding($basePrompt)
    {
        $user = User::findOrFail($this->document->getMeta('user_id'));
        $oraculum = new Oraculum($user, $this->meta['collection_name']);

        return $oraculum->query($basePrompt . $this->promptHelper->expandEmbeddedOn($this->meta['text_section'], [
            'tone' => $this->document->getMeta('tone'),
            'style' => $this->document->getMeta('style') ?? null,
            'keyword' => $this->meta['keyword']
        ]));
    }

    protected function queryGpt($basePrompt)
    {
        $chatGpt = new ChatGPT();
        return $chatGpt->request([
            [
                'role' => 'user',
                'content' =>  $basePrompt . $this->promptHelper->expandOn($this->meta['text_section'], [
                    'tone' => $this->document->getMeta('tone'),
                    'style' => $this->document->getMeta('style') ?? null,
                    'keyword' => $this->meta['keyword']
                ])
            ]
        ]);
    }
}
