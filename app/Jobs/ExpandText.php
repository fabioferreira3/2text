<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class ExpandText implements ShouldQueue, ShouldBeUnique
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
            $rawStructure = $this->document->getMeta('raw_structure');
            $order = $this->meta['order'];
            foreach ($rawStructure as $key => $section) {
                DocumentRepository::createTask($this->document->id, DocumentTaskEnum::EXPAND_TEXT_SECTION, [
                    'process_id' => $this->meta['process_id'],
                    'order' => $order,
                    'meta' => [
                        'text_section' => $section['content'],
                        'section_key' => $key,
                        'keyword' => $this->meta['keyword'],
                        'query_embedding' => $this->meta['query_embedding'] ?? false,
                        'collection_name' => $this->meta['collection_name'] ?? '',
                    ]
                ]);
                $order++;
            }

            DocumentRepository::createTask($this->document->id, DocumentTaskEnum::PUBLISH_TEXT_BLOCKS, [
                'process_id' => $this->meta['process_id'],
                'order' => $order,
                'meta' => []
            ]);

            DocumentRepository::createTask($this->document->id, DocumentTaskEnum::REGISTER_CONTENT_HISTORY, [
                'process_id' => $this->meta['process_id'],
                'order' => $order + 1,
                'meta' => []
            ]);

            DocumentRepository::createTask($this->document->id, DocumentTaskEnum::REGISTER_FINISHED_PROCESS, [
                'order' => 1000,
                'process_id' => $this->meta['process_id'],
                'meta' => []
            ]);

            DispatchDocumentTasks::dispatch($this->document);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand text: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'expand_text_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
