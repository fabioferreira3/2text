<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Exceptions\InvalidEmbeddingSummaryDataTypeException;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Summarizer\CreateFromFile;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Summarizer - CreateFromFile job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create();
        $job = new CreateFromFile($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers embed, summarize and broadcast tasks', function ($dataType) {
        Bus::fake([DispatchDocumentTasks::class]);

        $sourceFilePath = fake()->filePath();
        $document = Document::factory()->create([
            'meta' => [
                'max_words_count' => 250,
                'source' => $dataType,
                'source_file_path' => $sourceFilePath
            ]
        ]);

        $processId = Str::uuid();
        $job = new CreateFromFile($document, [
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
                'data_type' => $dataType,
                'source' => $sourceFilePath,
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
    })->with([DataType::DOCX->value, DataType::CSV->value, DataType::PDF->value]);

    it('throws an exception if an invalid data type', function ($dataType) {
        $document = Document::factory()->create([
            'meta' => [
                'max_words_count' => 250,
                'source' => $dataType
            ]
        ]);

        $processId = Str::uuid();
        $job = new CreateFromFile($document, [
            'process_id' => $processId
        ]);
        $job->handle();
    })->throws(InvalidEmbeddingSummaryDataTypeException::class)->with([
        DataType::TEXT->value,
        DataType::DOCS_SITE->value,
        DataType::MDX->value,
        DataType::NOTION->value,
        DataType::SITEMAP->value,
        DataType::WEB_PAGE->value,
        DataType::YOUTUBE->value
    ]);
});
