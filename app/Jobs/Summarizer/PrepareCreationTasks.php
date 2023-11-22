<?php

namespace App\Jobs\Summarizer;

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PrepareCreationTasks
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

        DispatchDocumentTasks::dispatch($this->document);

        CreateFromFreeText::dispatchIf(
            $this->document->getMeta('source') === SourceProvider::FREE_TEXT->value,
            $this->document,
            $this->params
        );
        CreateFromVideoStream::dispatchIf(
            $this->document->getMeta('source') === SourceProvider::YOUTUBE->value,
            $this->document,
            $this->params
        );
        CreateFromWebsite::dispatchIf(
            $this->document->getMeta('source') === SourceProvider::WEBSITE_URL->value,
            $this->document,
            $this->params
        );
        CreateFromFile::dispatchIf(in_array($this->document->getMeta('source'), [
            SourceProvider::CSV->value,
            SourceProvider::DOCX->value,
            SourceProvider::PDF->value
        ]), $this->document, $this->params);
    }
}
