<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Summarizer\CreateFromWebsite;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Summarizer - CreateFromWebsite job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create();
        $job = new CreateFromWebsite($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers embed, summarize and broadcast tasks', function () {
        Bus::fake([DispatchDocumentTasks::class]);

        $sourceUrl = fake()->url();
        $document = Document::factory()->create([
            'meta' => [
                'max_words_count' => 250,
                'source_url' => $sourceUrl
            ]
        ]);

        $processId = Str::uuid();
        $job = new CreateFromWebsite($document, [
            'process_id' => $processId
        ]);
        $job->handle();

        $embedTask = DocumentTask::where('process_id', $processId)
            ->where('name', DocumentTaskEnum::EMBED_SOURCE->value)->first();
        expect($embedTask)->toMatchArray([
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'order' => 1,
            'meta' => [
                'data_type' => DataType::WEB_PAGE->value,
                'source' => $sourceUrl,
                'collection_name' => $document->id
            ]
        ]);

        $summarizeTask = DocumentTask::where('process_id', $processId)
            ->where('name', DocumentTaskEnum::SUMMARIZE_CONTENT->value)->first();
        expect($summarizeTask)->toMatchArray([
            'name' => DocumentTaskEnum::SUMMARIZE_CONTENT->value,
            'job' => DocumentTaskEnum::SUMMARIZE_CONTENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'order' => 2,
            'meta' => [
                'query_embedding' => true,
                'max_words_count' => 250,
                'content' => null,
                'collection_name' => $document->id
            ]
        ]);

        $broadcastTask = DocumentTask::where('process_id', $processId)
            ->where('name', DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->value)->first();
        expect($broadcastTask)->toMatchArray([
            'name' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->value,
            'job' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'order' => 3,
            'meta' => [
                'event_name' => 'SummaryCompleted'
            ]
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });
});
