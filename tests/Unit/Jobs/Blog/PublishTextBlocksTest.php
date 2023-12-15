<?php

use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\User;
use App\Repositories\DocumentRepository;

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

describe('Blog - Publish Text Blocks job', function () {
    it('publishes normalized structure into text blocks', function () {
        $repo = new DocumentRepository($this->document);
        $repo->publishContentBlocks();

        $contentBlocks = DocumentContentBlock::where('document_id', $this->document->id)->get();
        expect($contentBlocks->count())->toBe(5);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'h1',
            'content' => 'I. Subheader',
            'prompt' => null,
            'prefix' => null,
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'h2',
            'content' => 'This is a title',
            'prompt' => null,
            'prefix' => null,
            'order' => 2
        ]);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'h2',
            'content' => 'II. Subheader',
            'prompt' => null,
            'prefix' => null,
            'order' => 3
        ]);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'p',
            'content' => 'This is a paragraph',
            'prompt' => null,
            'prefix' => null,
            'order' => 4
        ]);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'p',
            'content' => 'This is another paragraph',
            'prompt' => null,
            'prefix' => null,
            'order' => 5
        ]);
    });
})->group('blog');
