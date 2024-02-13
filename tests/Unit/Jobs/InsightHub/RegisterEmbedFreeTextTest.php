<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InsightHub\RegisterEmbedFreeText;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
});

describe('Insight Hub - RegisterEmbedText job', function () {
    it('can be serialized', function () {
        $job = new RegisterEmbedFreeText($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers the embed task', function () {
        Bus::fake([DispatchDocumentTasks::class]);
        $processId = Str::uuid();
        $job = new RegisterEmbedFreeText($this->document, [
            'process_id' => $processId,
            'source' => 'test content source here'
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'meta->data_type' => DataType::TEXT->value,
            'meta->collection_name' => $this->document->id,
            'meta->source' => 'test content source here',
            'order' => 1
        ]);
    });
})->group('insight-hub');
