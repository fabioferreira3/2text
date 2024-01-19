<?php

namespace App\Jobs\Blog;

use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelperFactory;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\RegisterProductUsage;
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

class CreateOutline implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $promptHelper;
    public DocumentRepository $repo;
    public OraculumFactoryInterface $oraculumFactory;
    public ChatGPTFactoryInterface $chatGptFactory;

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
    public function __construct(
        Document $document,
        array $meta = []
    ) {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = PromptHelperFactory::create($document->language->value);
        $this->repo = new DocumentRepository($this->document);
        $this->oraculumFactory = app(OraculumFactoryInterface::class);
        $this->chatGptFactory = app(ChatGPTFactoryInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->meta['query_embedding'] ?? false) {
                $response = $this->queryEmbedding();
            } else {
                $response = $this->queryGpt();
            }

            $this->repo->updateMeta('outline', $response['content']);
            $this->repo->updateMeta('raw_structure', DocumentHelper::parseOutlineToRawStructure($response['content']));

            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);
            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to generate outline');
        }
    }

    protected function queryEmbedding()
    {
        $user = User::findOrFail($this->document->getMeta('user_id'));
        $oraculum = $this->oraculumFactory->make($user, $this->meta['collection_name']);
        return $oraculum->query($this->promptHelper->writeEmbeddedOutline(
            [
                'tone' => $this->document->getMeta('tone'),
                'keyword' => $this->document->getMeta('keyword'),
                'style' => $this->document->getMeta('style') ?? null,
                'maxsubtopics' => $this->document->getMeta('target_headers_count') ?? 2,
                'context' => $this->document->getMeta('context')
            ]
        ));
    }

    protected function queryGpt()
    {
        $chatGpt = $this->chatGptFactory->make();
        return $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $this->promptHelper->writeOutline(
                    $this->document->getContext(),
                    [
                        'tone' => $this->document->getMeta('tone'),
                        'keyword' => $this->document->getMeta('keyword'),
                        'style' => $this->document->getMeta('style') ?? null,
                        'maxsubtopics' => $this->document->getMeta('target_headers_count') ?? 2
                    ]
                )
            ]
        ]);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_outline_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
