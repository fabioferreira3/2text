<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Summarizer\CreateFromFreeText;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Summarizer - CreateFromFreeText job', function () {
    it('registers summarize and broadcast tasks', function () {
        Bus::fake([DispatchDocumentTasks::class]);
        $document = Document::factory()->create([
            'meta' => [
                'max_words_count' => 250,
            ],
            'content' => 'content here'
        ]);
        $processId = Str::uuid();
        $job = new CreateFromFreeText($document, [
            'process_id' => $processId
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::SUMMARIZE_CONTENT->value,
            'job' => DocumentTaskEnum::SUMMARIZE_CONTENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->query_embedding' => false,
            'meta->max_words_count' => 250,
            'meta->content' => 'content here',
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->value,
            'job' => DocumentTaskEnum::BROADCAST_CUSTOM_EVENT->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->event_name' => 'SummaryCompleted',
            'order' => 2
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });
});
