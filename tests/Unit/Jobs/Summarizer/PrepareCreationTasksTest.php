<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Summarizer\CreateFromFile;
use App\Jobs\Summarizer\CreateFromFreeText;
use App\Jobs\Summarizer\CreateFromVideoStream;
use App\Jobs\Summarizer\CreateFromWebsite;
use App\Jobs\Summarizer\PrepareCreationTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;

describe('Summarizer - PrepareCreationTasks job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create();
        $job = new PrepareCreationTasks($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers remove embedding task', function () {
        $document = Document::factory()->create();
        $job = new PrepareCreationTasks($document, []);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $document->id,
            'job' => DocumentTaskEnum::REMOVE_EMBEDDINGS->getJob(),
            'status' => 'ready',
            'order' => 1,
            'meta->collection_name' => $document->id
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class);
    });

    it('dispatches a free text job', function () {
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value
            ]
        ]);
        $job = new PrepareCreationTasks($document, []);
        $job->handle();

        Bus::assertDispatched(CreateFromFreeText::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a website job', function () {
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::WEBSITE_URL->value
            ]
        ]);
        $job = new PrepareCreationTasks($document, []);
        $job->handle();

        Bus::assertDispatched(CreateFromWebsite::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a video stream job', function () {
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::YOUTUBE->value
            ]
        ]);
        $job = new PrepareCreationTasks($document, []);
        $job->handle();

        Bus::assertDispatched(CreateFromVideoStream::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a file job', function ($source) {
        $document = Document::factory()->create([
            'meta' => [
                'source' => $source
            ]
        ]);
        $job = new PrepareCreationTasks($document, []);
        $job->handle();

        Bus::assertDispatched(CreateFromFile::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
    });
})->with([SourceProvider::PDF->value, SourceProvider::DOCX->value, SourceProvider::CSV->value]);
