<?php

namespace App\Jobs;

use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpandOutline implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;
    public OraculumFactoryInterface $oraculumFactory;
    public ChatGPTFactoryInterface $chatGptFactory;

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

            $this->repo->updateMeta('first_pass', $response['content']);
            $this->repo->updateMeta('raw_structure', DocumentHelper::parseHtmlTagsToRawStructure($response['content']));
            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand outline: ' . $e->getMessage());
        }
    }

    protected function queryEmbedding()
    {
        $user = User::findOrFail($this->document->getMeta('user_id'));
        $oraculum = $this->oraculumFactory->make($user, $this->meta['collection_name']);

        return $oraculum->query($this->promptHelper->writeEmbeddedFirstPass(
            $this->document->getRawStructureDescription(),
            [
                'tone' => $this->document->getMeta('tone'),
                'style' => $this->document->getMeta('style') ?? null
            ]
        ));
    }

    protected function queryGpt()
    {
        $chatGpt = $this->chatGptFactory->make();
        return $chatGpt->request([
            [
                'role' => 'user',
                'content' => $this->promptHelper->writeFirstPass(
                    $this->document->getRawStructureDescription(),
                    [
                        'tone' => $this->document->getMeta('tone'),
                        'style' => $this->document->getMeta('style') ?? null
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
        return 'expand_outline_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
