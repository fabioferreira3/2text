<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InsightHub\RegisterEmbedWebsite;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Insight Hub - RegisterEmbedWebsite job', function () {
    it('registers the embed task', function () {
        $url = fake()->url();
        Bus::fake([DispatchDocumentTasks::class]);
        $document = Document::factory()->create();
        $processId = Str::uuid();
        $job = new RegisterEmbedWebsite($document, [
            'process_id' => $processId,
            'source' => $url
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->data_type' => DataType::WEB_PAGE->value,
            'meta->collection_name' => $document->id,
            'meta->source' => $url,
            'order' => 1
        ]);
    });
})->group('insight-hub');
