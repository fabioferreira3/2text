<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Summarizer\CreateFromVideoStream;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Summarizer - CreateFromVideoStream job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create();
        $job = new CreateFromVideoStream($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers download, transcribe, summarize and broadcast tasks', function () {
        Bus::fake([DispatchDocumentTasks::class]);

        $sourceUrl = fake()->url();
        $document = Document::factory()->create([
            'meta' => [
                'max_words_count' => 250,
                'source_url' => $sourceUrl
            ]
        ]);

        $processId = Str::uuid();
        $job = new CreateFromVideoStream($document, [
            'process_id' => $processId
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::DOWNLOAD_SUBTITLES->value,
            'job' => DocumentTaskEnum::DOWNLOAD_SUBTITLES->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->source_url' => $sourceUrl,
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO->value,
            'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->abort_when_context_present' => true,
            'order' => 2
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::SUMMARIZE_CONTENT->value,
            'job' => DocumentTaskEnum::SUMMARIZE_CONTENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->query_embedding' => false,
            'meta->max_words_count' => 250,
            'meta->content' => null,
            'order' => 3
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->value,
            'job' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->event_name' => 'SummaryCompleted',
            'order' => 4
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });
});
