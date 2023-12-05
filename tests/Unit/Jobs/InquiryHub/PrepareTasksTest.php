<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InquiryHub\PrepareTasks;
use App\Jobs\InquiryHub\RegisterEmbedFile;
use App\Jobs\InquiryHub\RegisterEmbedFreeText;
use App\Jobs\InquiryHub\RegisterEmbedVideoStream;
use App\Jobs\InquiryHub\RegisterEmbedWebsite;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'meta' => []
    ]);
});

afterEach(function () {
    Bus::assertDispatched(DispatchDocumentTasks::class);
    $this->assertDatabaseHas('document_tasks', [
        'name' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->value,
        'job' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->getJob(),
        'document_id' => $this->document->id,
        'meta->event_name' => 'EmbedCompleted',
        'order' => 999
    ]);
});

describe('Inquiry Hub - PrepareTasks job', function () {
    it('dispatches an embed free text job', function () {
        $document = $this->document;
        $document->updateMeta('source', SourceProvider::FREE_TEXT->value);

        $job = new PrepareTasks($document, [
            'source' => fake()->text(),
            'source_type' => $document->getMeta('source'),
            'source_url' => null,
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedFreeText::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedWebsite::class);
        Bus::assertNotDispatched(RegisterEmbedVideoStream::class);
        Bus::assertNotDispatched(RegisterEmbedFile::class);
    });

    it('dispatches an embed website job', function () {
        $document = $this->document;
        $document->updateMeta('source', SourceProvider::WEBSITE_URL->value);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => fake()->url(),
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedWebsite::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
        Bus::assertNotDispatched(RegisterEmbedVideoStream::class);
        Bus::assertNotDispatched(RegisterEmbedFile::class);
    });

    it('dispatches an embed video stream job', function () {
        $document = $this->document;
        $document->updateMeta('source', SourceProvider::YOUTUBE->value);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => fake()->url(),
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedVideoStream::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedWebsite::class);
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
        Bus::assertNotDispatched(RegisterEmbedFile::class);
    });

    it('dispatches an embed file job', function ($source) {
        $document = $this->document;
        $document->updateMeta('source', $source);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => null,
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedFile::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedVideoStream::class);
        Bus::assertNotDispatched(RegisterEmbedWebsite::class);
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
    })->with([SourceProvider::PDF->value, SourceProvider::DOCX->value, SourceProvider::CSV->value]);
});
