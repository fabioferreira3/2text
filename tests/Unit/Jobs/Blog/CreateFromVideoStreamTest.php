<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\CreateFromVideoStream;
use App\Jobs\Blog\RegisterCreationTasks;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->sourceUrls = [fake()->url(), fake()->url(), fake()->url()];
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'source' => SourceProvider::YOUTUBE->value,
            'source_urls' => $this->sourceUrls
        ]
    ]);
});

describe(
    'Blog - CreateFromVideoStream job',
    function () {
        it('registers common tasks', function () {
            Bus::fake([DispatchDocumentTasks::class, RegisterCreationTasks::class]);

            $processId = Str::uuid();
            $job = new CreateFromVideoStream($this->document, [
                'process_id' => $processId
            ]);
            $job->handle();

            foreach ($this->sourceUrls as $key => $sourceUrl) {
                $this->assertDatabaseHas('document_tasks', [
                    'name' => DocumentTaskEnum::EXTRACT_AND_EMBED_AUDIO->value,
                    'job' => DocumentTaskEnum::EXTRACT_AND_EMBED_AUDIO->getJob(),
                    'document_id' => $this->document->id,
                    'process_id' => $processId,
                    'order' => $key + 2,
                    'meta->source_url' => $sourceUrl,
                    'meta->collection_name' => $this->document->id,
                ]);
            }

            Bus::assertDispatchedSync(RegisterCreationTasks::class, function ($job) use ($processId) {
                return
                    $job->document->id === $this->document->id &&
                    $job->params['next_order'] === 5 &&
                    $job->params['process_id'] == $processId &&
                    $job->params['query_embedding'] === true &&
                    $job->params['collection_name'] === $this->document->id;
            });
            Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
                return $job->document->id === $this->document->id;
            });
        });
    }
)->group('blog');
