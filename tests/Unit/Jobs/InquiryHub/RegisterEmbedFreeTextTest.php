<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InquiryHub\RegisterEmbedFreeText;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;


describe('Inquiry Hub - RegisterEmbedText job', function () {
    it('registers the embed task', function () {
        Bus::fake([DispatchDocumentTasks::class]);
        $document = Document::factory()->create();
        $processId = Str::uuid();
        $job = new RegisterEmbedFreeText($document, [
            'process_id' => $processId,
            'source' => 'test content source here'
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $document->id,
            'process_id' => $processId,
            'meta->data_type' => DataType::TEXT->value,
            'meta->collection_name' => $document->id,
            'meta->source' => 'test content source here',
            'order' => 1
        ]);
    });
});
