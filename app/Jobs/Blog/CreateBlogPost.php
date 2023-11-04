<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateBlogPost
{
    use Dispatchable, SerializesModels;

    protected Document $document;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = [
            ...$params,
            'process_id' => Str::uuid()
        ];
    }

    public function handle()
    {
        $tasksCount = $this->defineTasksCount();

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::GENERATE_FINISHED_NOTIFICATION,
            [
                'process_id' => Str::uuid(),
                'meta' => [],
                'order' => 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::GENERATE_AI_THOUGHTS,
            [
                'process_id' => Str::uuid(),
                'meta' => [
                    'sentences_count' => $tasksCount
                ],
                'order' => 1
            ]
        );
        DispatchDocumentTasks::dispatch($this->document);

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::REMOVE_EMBEDDINGS,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'collection_name' => $this->document->id
                ],
                'order' => 1
            ]
        );

        CreateFromFreeText::dispatchIf($this->params['source'] === 'free_text', $this->document, $this->params);
        CreateFromVideoStream::dispatchIf($this->params['source'] === 'youtube', $this->document, $this->params);
        CreateFromWebsite::dispatchIf($this->params['source'] === 'website_url', $this->document, $this->params);
    }

    public function defineTasksCount()
    {
        $tasksCount = 6 + $this->document->getMeta('target_headers_count');
        if (in_array($this->document->getMeta('source'), [
            SourceProvider::WEBSITE_URL->value,
            SourceProvider::YOUTUBE->value
        ])) {
            $tasksCount += count($this->document->getMeta('source_urls'));
        }

        if ($this->document->getMeta('generate_image') ?? false) {
            $tasksCount += 1;
        }

        $repo = new DocumentRepository(($this->document));
        $repo->updateMeta('total_tasks_count', $tasksCount);
        $repo->updateMeta('completed_tasks_count', 0);

        return $tasksCount;
    }
}
