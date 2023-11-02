<?php

namespace App\Jobs\Paraphraser;

use App\Events\Paraphraser\TextParaphrased;
use App\Helpers\PromptHelper;
use App\Jobs\RegisterProductUsage;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParaphraseText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected $document;
    protected $repo;
    protected array $meta;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->meta = $params;
    }

    public function handle()
    {
        try {
            $this->document = $this->document->fresh();
            $this->repo = new DocumentRepository($this->document);
            $promptHelper = new PromptHelper($this->document->language->value);
            $chatGpt = new ChatGPT();
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' => $promptHelper->paraphrase(
                        $this->meta['text'],
                        $this->meta['tone']
                    )
                ]
            ]);

            if ($this->meta['add_content_block'] ?? false) {
                $this->document->contentBlocks()->save(new DocumentContentBlock([
                    'type' => 'text',
                    'content' => $response['content'],
                    'prompt' => null,
                    'order' => $this->meta['sentence_order']
                ]));
            }

            $this->repo->addHistory(
                [
                    'field' => 'paraphrased_text',
                    'content' => $response['content']
                ]
            );

            RegisterProductUsage::dispatch($this->document->account, [
                ...$response['token_usage'],
                'meta' => ['document_id' => $this->document->id]
            ]);
            TextParaphrased::dispatch($this->document, [
                'user_id' => $this->document->meta['user_id'],
                'process_id' => $this->meta['process_id']
            ]);

            $this->jobSucceded();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->jobFailed();
        }
    }
}
