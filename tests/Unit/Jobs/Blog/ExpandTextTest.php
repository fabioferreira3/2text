<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\ExpandText;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->document = Document::factory()->create([
        'meta' => [
            'raw_structure' => [
                [
                    "subheader" => "<h1>I. Subheader</h1>",
                    "content" => "<h2>This is a title</h2>"
                ],
                [
                    "subheader" => "<h2>II. Subheader</h2>",
                    "content" => "<p>This is a paragraph</p>
                    <p>This is another paragraph</p>"
                ]
            ]
        ]
    ]);
});

describe('Blog - Expand Text job', function () {
    it('can be serialized', function () {
        $job = new ExpandText($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers expands text sections tasks', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $initialOrder = 1;
        $processId = '728b394f-118b-40aa-849e-55b3339969f5';
        $job = new ExpandText($this->document, [
            'order' => $initialOrder,
            'process_id' => $processId,
            'keyword' => 'a keyword',
            'query_embedding' => true,
            'collection_name' => $this->document->id
        ]);
        $job->handle();

        foreach ($this->document->getMeta('raw_structure') as $section) {
            $this->assertDatabaseHas('document_tasks', [
                'document_id' => $this->document->id,
                'process_id' => $processId,
                'job' => DocumentTaskEnum::EXPAND_TEXT_SECTION->getJob(),
                'status' => 'ready',
                'order' => $initialOrder,
                'meta->text_section' => $section['content'],
                'meta->keyword' => 'a keyword',
                'meta->query_embedding' => true,
                'meta->collection_name' => $this->document->id
            ]);
            $initialOrder++;
        }

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'job' => DocumentTaskEnum::PUBLISH_TEXT_BLOCKS->getJob(),
            'status' => 'ready',
            'order' => $initialOrder
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'job' => DocumentTaskEnum::REGISTER_FINISHED_PROCESS->getJob(),
            'status' => 'ready',
            'order' => 1000
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class);
    });
})->group('blog');
