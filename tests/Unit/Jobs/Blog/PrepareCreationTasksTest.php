<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Blog\CreateFromFile;
use App\Jobs\Blog\CreateFromFreeText;
use App\Jobs\Blog\CreateFromVideoStream;
use App\Jobs\Blog\CreateFromWebsite;
use App\Jobs\Blog\PrepareCreationTasks;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->user = User::factory()->create();
    $repo = new DocumentRepository();
    $this->params = [
        'source' => SourceProvider::FREE_TEXT->value,
        'context' => 'some context',
        'language' => Language::ENGLISH->value,
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

describe('Blog - PrepareCreationTasks job', function () {
    it('registers finished blog post notification job', function () {
        Bus::fake(DispatchDocumentTasks::class);
        $job = new PrepareCreationTasks($this->document, $this->params);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::GENERATE_FINISHED_BLOG_POST_NOTIFICATION->getJob(),
            'status' => 'ready',
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::GENERATE_AI_THOUGHTS->getJob(),
            'status' => 'ready',
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'job' => DocumentTaskEnum::REMOVE_EMBEDDINGS->getJob(),
            'status' => 'ready',
            'order' => 1
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class);
    });

    it('dispatches a free text job', function () {
        $job = new PrepareCreationTasks($this->document, $this->params);
        $job->handle();

        Bus::assertDispatched(CreateFromFreeText::class, function ($job) {
            return $job->document === $this->document;
        });
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a website job', function () {
        $job = new PrepareCreationTasks(
            $this->document,
            [...$this->params, 'source' => SourceProvider::WEBSITE_URL->value]
        );
        $job->handle();

        Bus::assertDispatched(CreateFromWebsite::class, function ($job) {
            return $job->document->id === $this->document->id;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a video stream job', function () {
        $job = new PrepareCreationTasks(
            $this->document,
            [...$this->params, 'source' => SourceProvider::YOUTUBE->value]
        );
        $job->handle();

        Bus::assertDispatched(CreateFromVideoStream::class, function ($job) {
            return $job->document->id === $this->document->id;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromFile::class);
    });

    it('dispatches a file job', function () {
        $job = new PrepareCreationTasks(
            $this->document,
            [...$this->params, 'source' => SourceProvider::PDF->value]
        );
        $job->handle();

        Bus::assertDispatched(CreateFromFile::class, function ($job) {
            return $job->document->id === $this->document->id;
        });
        Bus::assertNotDispatched(CreateFromFreeText::class);
        Bus::assertNotDispatched(CreateFromWebsite::class);
        Bus::assertNotDispatched(CreateFromVideoStream::class);
    });
})->group('blog');
