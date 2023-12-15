<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\RegisterCreationTasks;
use App\Models\User;
use App\Repositories\DocumentRepository;

beforeEach(function () {
    $this->user = User::factory()->create();
    $repo = new DocumentRepository();
    $this->params = [
        'process_id' => '4c8df953-5988-4a25-af18-12a08708409b',
        'next_order' => 1,
        'source' => SourceProvider::FREE_TEXT->value,
        'context' => 'some context',
        'language' => Language::ENGLISH->value,
        'query_embedding' => true,
        'meta' => [
            'source_urls' => ['https://experior.ai'],
            'source_file_path' => 'document/file',
            'target_headers_count' => 5,
            'tone' => 'default',
            'style' => 'default',
            'keyword' => 'a keyword',
            'img_prompt' => 'image prompt',
            'generate_image' => true
        ]
    ];
    $this->document = $repo->createBlogPost($this->params);
});

describe('Blog - RegisterCreationTasksTest job', function () {
    it('registers image generation job', function () {
        $job = new RegisterCreationTasks($this->document, $this->params);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::GENERATE_IMAGE->getJob(),
            'status' => 'ready',
            'meta->prompt' => 'image prompt',
            'order' => 1
        ]);
    });

    it('registers the general blog creation tasks', function () {
        $job = new RegisterCreationTasks($this->document, $this->params);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::CREATE_OUTLINE->getJob(),
            'status' => 'ready',
            'meta->query_embedding' => true,
            'meta->collection_name' => '',
            'order' => 2
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::EXPAND_OUTLINE->getJob(),
            'status' => 'ready',
            'meta->query_embedding' => true,
            'meta->collection_name' => '',
            'order' => 3
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::EXPAND_TEXT->getJob(),
            'status' => 'ready',
            'meta->query_embedding' => true,
            'meta->collection_name' => '',
            'meta->keyword' => 'a keyword',
            'order' => 4
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::CREATE_TITLE->getJob(),
            'status' => 'ready',
            'meta->query_embedding' => true,
            'meta->collection_name' => '',
            'order' => 5
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::CREATE_METADESCRIPTION->getJob(),
            'status' => 'ready',
            'meta->query_embedding' => true,
            'meta->collection_name' => '',
            'order' => 6
        ]);
    });
})->group('blog');
