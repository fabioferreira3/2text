<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Jobs\InsightHub\RegisterEmbedFile;
use App\Models\Document;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->processId = Str::uuid();
});


describe('Insight Hub - RegisterEmbedFile job', function () {
    it('registers the embed task', function ($sourceType) {
        $job = new RegisterEmbedFile($this->document, [
            'process_id' => $this->processId,
            'source_type' => $sourceType
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $this->processId,
            'meta->data_type' => DataType::tryFrom($sourceType)->value,
            'meta->collection_name' => $this->document->id,
            'order' => 1
        ]);
    })->with([
        DataType::DOCX->value,
        DataType::WEB_PAGE->value,
        DataType::PDF->value,
        DataType::YOUTUBE->value
    ]);
})->group('insight-hub');
