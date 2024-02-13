<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InsightHub\EmbedYoutubeUrl;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->processId = Str::uuid();
});


describe('Insight Hub - EmbedYoutubeUrl job', function () {
    it('registers the embed task', function () {
        Bus::fake();
        $job = new EmbedYoutubeUrl($this->document, [
            'process_id' => $this->processId,
            'source' => 'https://experior.ai'
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $this->processId,
            'meta->data_type' => DataType::TEXT->value,
            'meta->source' => 'https://experior.ai',
            'meta->collection_name' => $this->document->id,
            'order' => 1
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
            return $job->document->id === $this->document->id;
        });
    });
})->group('insight-hub');
