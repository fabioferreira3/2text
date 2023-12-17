<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PublishTextBlock implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
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
            if ($this->meta['text'] ?? $this->document->getMeta('context')) {
                $contentBlock = $this->document->contentBlocks()->save(new DocumentContentBlock([
                    'type' => 'text',
                    'content' => $this->meta['text'] ?? $this->document->getMeta('context'),
                    'prompt' => null,
                    'order' => 1
                ]));

                if ($this->meta['target_language'] ?? false) {
                    DocumentRepository::createTask(
                        $this->document->id,
                        DocumentTaskEnum::TRANSLATE_TEXT_BLOCK,
                        [
                            'order' => 1,
                            'process_id' => Str::uuid(),
                            'meta' => [
                                'content_block_id' => $contentBlock->id,
                                'target_language' => $this->meta['target_language']
                            ]
                        ]
                    );
                    DispatchDocumentTasks::dispatch($this->document);
                }
            }

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to publish text block: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'publishing_text_block_' . $this->document->id;
    }
}
