<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\CreateFromFile;
use App\Jobs\Blog\RegisterCreationTasks;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->filePath = fake()->filePath();
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'source' => SourceProvider::PDF->value,
            'source_file_path' => $this->filePath
        ]
    ]);
});

describe(
    'Blog - CreateFromFile job',
    function () {
        it('registers common tasks', function () {
            Bus::fake([DispatchDocumentTasks::class, RegisterCreationTasks::class]);
            $processId = Str::uuid();
            $job = new CreateFromFile($this->document, [
                'process_id' => $processId
            ]);
            $job->handle();

            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::EMBED_SOURCE->value,
                'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
                'document_id' => $this->document->id,
                'process_id' => $processId,
                'order' => 2,
                'meta->data_type' => DataType::PDF->value,
                'meta->source' => $this->filePath,
                'meta->collection_name' => $this->document->id,
            ]);

            Bus::assertDispatchedSync(RegisterCreationTasks::class, function ($job) use ($processId) {
                return
                    $job->document->id === $this->document->id &&
                    $job->params['next_order'] === 2 &&
                    $job->params['process_id'] == $processId &&
                    $job->params['query_embedding'] === true &&
                    $job->params['collection_name'] === $this->document->id;
            });
            Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
                return $job->document->id === $this->document->id;
            });
        });
    }
);
