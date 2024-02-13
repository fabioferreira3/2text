<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\PublishTextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->document = Document::factory()->create();
});

describe('PublishTextBlock job', function () {
    it('can be serialized', function () {
        $job = new PublishTextBlock($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('publishes a text block from a document context', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $this->document->updateMeta('context', 'some context');
        $job = new PublishTextBlock($this->document, [
            'target_language' => 'pt'
        ]);
        $job->handle();

        $contentBlock = DocumentContentBlock::first();

        $this->assertDatabaseHas('document_content_blocks', [
            'id' => $contentBlock->id,
            'document_id' => $this->document->id,
            'content' => 'some context',
            'type' => 'text',
            'prompt' => null,
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::TRANSLATE_TEXT_BLOCK->value,
            'job' => DocumentTaskEnum::TRANSLATE_TEXT_BLOCK->getJob(),
            'document_id' => $this->document->id,
            'meta->content_block_id' => $contentBlock->id,
            'meta->target_language' => 'pt',
            'order' => 1
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
            return $job->document->id === $this->document->id;
        });
    });

    it('publishes a text block from a text', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $job = new PublishTextBlock($this->document, [
            'text' => 'some text here'
        ]);
        $job->handle();

        $contentBlock = DocumentContentBlock::first();

        $this->assertDatabaseHas('document_content_blocks', [
            'id' => $contentBlock->id,
            'document_id' => $this->document->id,
            'content' => 'some text here',
            'type' => 'text',
            'prompt' => null,
            'order' => 1
        ]);

        Bus::assertNotDispatched(DispatchDocumentTasks::class);
    });
});
