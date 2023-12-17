<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\PublishTextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Illuminate\Support\Facades\Bus;

describe('PublishTextBlock job', function () {
    it('publishes a text block from a document context', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $document = Document::factory()->create([
            'meta' => [
                'context' => 'some context'
            ]
        ]);
        $job = new PublishTextBlock($document, [
            'target_language' => 'pt'
        ]);
        $job->handle();

        $contentBlock = DocumentContentBlock::first();

        $this->assertDatabaseHas('document_content_blocks', [
            'id' => $contentBlock->id,
            'document_id' => $document->id,
            'content' => 'some context',
            'type' => 'text',
            'prompt' => null,
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::TRANSLATE_TEXT_BLOCK->value,
            'job' => DocumentTaskEnum::TRANSLATE_TEXT_BLOCK->getJob(),
            'document_id' => $document->id,
            'meta->content_block_id' => $contentBlock->id,
            'meta->target_language' => 'pt',
            'order' => 1
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });

    it('publishes a text block from a text', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $document = Document::factory()->create();
        $job = new PublishTextBlock($document, [
            'text' => 'some text here'
        ]);
        $job->handle();

        $contentBlock = DocumentContentBlock::first();

        $this->assertDatabaseHas('document_content_blocks', [
            'id' => $contentBlock->id,
            'document_id' => $document->id,
            'content' => 'some text here',
            'type' => 'text',
            'prompt' => null,
            'order' => 1
        ]);

        Bus::assertNotDispatched(DispatchDocumentTasks::class);
    });
});
