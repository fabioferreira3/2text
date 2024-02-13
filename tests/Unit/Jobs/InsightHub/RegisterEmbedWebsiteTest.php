<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InsightHub\RegisterEmbedWebsite;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
});

describe('Insight Hub - RegisterEmbedWebsite job', function () {
    it('can be serialized', function () {
        $job = new RegisterEmbedWebsite($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers the embed task', function () {
        $url = fake()->url();
        Bus::fake([DispatchDocumentTasks::class]);
        $processId = Str::uuid();
        $job = new RegisterEmbedWebsite($this->document, [
            'process_id' => $processId,
            'source' => $url
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'meta->data_type' => DataType::WEB_PAGE->value,
            'meta->collection_name' => $this->document->id,
            'meta->source' => $url,
            'order' => 1
        ]);
    });
})->group('insight-hub');
