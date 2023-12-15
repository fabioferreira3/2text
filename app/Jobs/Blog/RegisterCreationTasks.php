<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Helpers\MediaHelper;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Talendor\StabilityAI\Enums\StylePreset;

class RegisterCreationTasks
{
    use Dispatchable, SerializesModels;

    public Document $document;
    public $repo;
    public array $params;
    public $mediaHelper;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
        $this->repo = new DocumentRepository($document);
        $this->mediaHelper = new MediaHelper();
    }

    public function handle()
    {
        if ($this->params['meta']['generate_image'] ?? false) {
            $imageSize = $this->mediaHelper->getImageSizeByDocumentType($this->document);
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::GENERATE_IMAGE,
                [
                    'order' => 1,
                    'process_id' => Str::uuid(),
                    'meta' => [
                        'prompt' => $this->params['meta']['img_prompt'],
                        'height' => $imageSize['height'],
                        'width' => $imageSize['width'],
                        'quality' => 'standard',
                        'style_preset' => StylePreset::DIGITAL_ART->value,
                        'steps' => 21,
                        'samples' => 1,
                        'add_content_block' => true
                    ]
                ]
            );
        }
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_OUTLINE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'query_embedding' =>  $this->params['query_embedding'] ?? false,
                    'collection_name' => $this->params['collection_name'] ?? ''
                ],
                'order' => $this->params['next_order'] + 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EXPAND_OUTLINE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'query_embedding' => $this->params['query_embedding'] ?? false,
                    'collection_name' => $this->params['collection_name'] ?? ''
                ],
                'order' => $this->params['next_order'] + 2
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EXPAND_TEXT,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'query_embedding' => $this->params['query_embedding'] ?? false,
                    'collection_name' => $this->params['collection_name'] ?? '',
                    'keyword' => $this->document->getMeta('keyword'),
                ],
                'order' => $this->params['next_order'] + 3
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'query_embedding' => $this->params['query_embedding'] ?? false,
                    'collection_name' => $this->params['collection_name'] ?? ''
                ],
                'order' => $this->params['next_order'] + 4
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_METADESCRIPTION,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [
                    'query_embedding' => $this->params['query_embedding'] ?? false,
                    'collection_name' => $this->params['collection_name'] ?? ''
                ],
                'order' => $this->params['next_order'] + 5
            ]
        );
    }
}
