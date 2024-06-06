<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Factories\LLMFactory;
use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\RegisterAppUsage;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExpandOutline implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public PromptHelper $promptHelper;

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

            $this->document->updateMeta('first_pass', $response['content']);
            $this->document->updateMeta(
                'raw_structure',
                DocumentHelper::parseHtmlTagsToRawStructure($response['content'])
            );
            RegisterAppUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::EXPAND_OUTLINE->value
                ]
            ]);
            $this->jobSucceded();
        } catch (HttpException $e) {
            $this->handleError($e, 'Failed to expand outline');
        } catch (Exception $e) {
            $this->jobFailed();
        }
    }

    protected function queryEmbedding()
    {
        $user = User::findOrFail($this->document->getMeta('user_id'));
        $oraculumFactory = App::make(OraculumFactoryInterface::class);
        $oraculum = $oraculumFactory->make($user, $this->meta['collection_name']);

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
        $llm = app(LLMFactory::class)->make('chatgpt');
        return $llm->request([
            [
                'role' => 'user',
                'content' => $this->promptHelper->writeFirstPass(
                    $this->document->getRawStructureDescription(),
                    [
                        'tone' => $this->document->getMeta('tone'),
                        'style' => $this->document->getMeta('style') ?? null
                    ]
                ),
                // 'task' => 'expand_outline'
            ]
        ]);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        $id = $this->meta['process_id'] ?? $this->document->id;
        return 'expand_outline_' . $id;
    }
}
