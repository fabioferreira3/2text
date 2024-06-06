<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Factories\LLMFactory;
use App\Helpers\PromptHelper;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExpandTextSection implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;
    public OraculumFactoryInterface $oraculumFactory;

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
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10, 15];
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
            $this->oraculumFactory = app(OraculumFactoryInterface::class);

            $rawStructure = $this->document->getMeta('raw_structure');
            $normalizedStructure = $this->document->normalized_structure;
            $basePrompt = $this->promptHelper->givenFollowingText($normalizedStructure);

            if ($this->meta['query_embedding'] ?? false) {
                $response = $this->queryEmbedding($basePrompt);
            } else {
                $response = $this->queryGpt($basePrompt);
            }

            $rawStructure[$this->meta['section_key']]['content'] = $response['content'];
            $this->repo->updateMeta('raw_structure', $rawStructure);
            RegisterAppUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::EXPAND_TEXT_SECTION->value
                ]
            ]);
            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to expand text');
        }
    }

    protected function queryEmbedding($basePrompt)
    {
        $user = User::findOrFail($this->document->getMeta('user_id'));
        $oraculum = $this->oraculumFactory->make($user, $this->meta['collection_name']);

        return $oraculum->query($basePrompt . $this->promptHelper->expandEmbeddedOn($this->meta['text_section'], [
            'tone' => $this->document->getMeta('tone'),
            'style' => $this->document->getMeta('style') ?? null,
            'keyword' => $this->meta['keyword']
        ]));
    }

    protected function queryGpt($basePrompt)
    {
        $llm = app(LLMFactory::class)->make('chatgpt');
        return $llm->request([
            [
                'role' => 'user',
                'content' =>  $basePrompt . $this->promptHelper->expandOn($this->meta['text_section'], [
                    'tone' => $this->document->getMeta('tone'),
                    'style' => $this->document->getMeta('style') ?? null,
                    'keyword' => $this->meta['keyword']
                ]),
                //    'task' => 'expand_text_section'
            ]
        ]);
    }
}
