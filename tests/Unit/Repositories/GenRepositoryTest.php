<?php

use App\Adapters\ImageGeneratorHandler;
use App\Enums\DocumentTaskEnum;
use App\Events\ProcessFinished;
use App\Factories\LLMFactory;
use App\Interfaces\LLMFactoryInterface;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\DocumentTask;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

beforeEach(function () {
    // $this->factoryInterface = Mockery::mock(LLMFactoryInterface::class);
    // $this->factoryInterface->shouldReceive('request')->withArgs(function ($arg) {
    //     return is_array($arg);
    // })->andReturn($this->aiModelResponseResponse);
    // $llmFactory = Mockery::mock(LLMFactory::class);
    // $llmFactory->shouldReceive('make')->andReturn($this->factoryInterface);
    // $this->app->instance(LLMFactory::class, $llmFactory);
});

describe('GenRepository', function () {
    it('generates a title', function () {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateTitle($document, 'some context here');
        $expectedTitle = 'AI content generated';
        expect($response['content'])->toBe($expectedTitle);
        expect($document->fresh()->title)->toBe($expectedTitle);
        expect($repo->response)->toBe($this->aiModelResponseResponse);
    });

    it('generates an embedded title', function () {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateEmbeddedTitle($document, 'collection name');
        $expectedTitle = 'AI content generated';
        expect($response['content'])->toBe($expectedTitle);
        expect($document->fresh()->title)->toBe($expectedTitle);
        expect($repo->response)->toBe($this->aiModelResponseResponse);
    });

    it('generates meta description', function () {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateMetaDescription($document);
        expect($response)->toBe($this->aiModelResponseResponse);
    });

    it('generates a summary', function () {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateSummary($document, [
            'content' => 'some content',
            'max_words_count' => 500
        ]);
        expect($response)->toBe($this->aiModelResponseResponse);
    });

    it('generates a embedded summary', function () {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateSummary($document, [
            'content' => 'some content',
            'max_words_count' => 500
        ]);
        expect($response)->toBe($this->aiModelResponseResponse);
    });

    it('generates social media posts', function ($platform) {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateSocialMediaPost($document, $platform);
        expect($response)->toBe($this->aiModelResponseResponse);
    })->with(['facebook', 'linkedin', 'instagram', 'twitter']);

    it('generates embedded social media posts', function ($platform) {
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $response = $repo->generateEmbeddedSocialMediaPost($document, $platform, 'collection name');
        expect($response)->toBe($this->aiModelResponseResponse);
    })->with(['facebook', 'linkedin', 'instagram', 'twitter']);

    it('rewrites a text block and updates it', function () {
        $documentContentBlock = DocumentContentBlock::factory()->create();
        $repo = new GenRepository();
        $response = $repo->rewriteTextBlock($documentContentBlock, ['prompt' => 'some prompt']);
        expect($response['content'])->toBe('AI content generated');
        expect($documentContentBlock->fresh()->content)->toBe('AI content generated');
    });

    it('generates an image and adds a content block to the document', function () {
        Event::fake(ProcessFinished::class);

        $document = Document::factory()->create();
        $processId = (string) Str::uuid();
        $documentTask = DocumentTask::factory()->create([
            'document_id' => $document->id,
            'process_id' => $processId
        ]);

        $params = [
            'prompt' => 'A beautiful landscape',
            'add_content_block' => true,
            'process_id' => $processId,
        ];

        $this->mockHandler = Mockery::mock(ImageGeneratorHandler::class);
        $this->app->instance(ImageGeneratorHandler::class, $this->mockHandler);

        $this->mockHandler->shouldReceive('handle')
            ->once()
            ->with('textToImage', $params)
            ->andReturn([
                'fileName' => 'landscape.jpg',
                'imageData' => 'image data here',
            ]);

        $genRepository = new GenRepository();
        $genRepository->generateImage($document, $params);

        $document->refresh();

        $this->assertDatabaseHas('media_files', [
            'meta->document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $document->id,
            'type' => 'media_file_image',
            'prompt' => $params['prompt'],
        ]);

        Event::assertDispatched(ProcessFinished::class, function ($event) use ($documentTask) {
            return $event->documentTask->id === $documentTask->id;
        });
    });

    it('translates text', function () {
        $text = "Hello, world!";
        $targetLanguage = "es";

        $genRepository = new GenRepository();
        $response = $genRepository->translateText($text, $targetLanguage);

        expect($response)->toEqual($this->aiModelResponseResponse);
    });

    it('registers paraphrase document tasks', function () {
        Bus::fake();
        $document = Document::factory()->create([
            'meta' => [
                'tone' => 'funny',
                'add_content_block' => true,
                'sentences' => [
                    [
                        'text' => 'sentence 1',
                        'sentence_order' => 1
                    ],
                    [
                        'text' => 'sentence 2',
                        'sentence_order' => 2
                    ]
                ]
            ]
        ]);
        $genRepository = new GenRepository();
        $genRepository->registerParaphraseDocumentTasks($document);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $document->id,
            'job' => DocumentTaskEnum::PARAPHRASE_TEXT->getJob(),
            'order' => 1,
            'meta->text' => 'sentence 1',
            'meta->tone' => 'funny',
            'meta->sentence_order' => 1,
            'meta->add_content_block' => true,
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $document->id,
            'job' => DocumentTaskEnum::PARAPHRASE_TEXT->getJob(),
            'order' => 1,
            'meta->text' => 'sentence 2',
            'meta->tone' => 'funny',
            'meta->sentence_order' => 2,
            'meta->add_content_block' => true,
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });

    it('registers a text to audio task', function () {
        Bus::fake();
        $voiceId = (string) Str::uuid();
        $processId = (string) Str::uuid();

        $document = Document::factory()->create();
        $genRepository = new GenRepository();
        $genRepository->registerTextToAudioTask($document, [
            'voice_id' => $voiceId,
            'process_id' => $processId,
            'input_text' => 'some text here'
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $document->id,
            'job' => DocumentTaskEnum::TEXT_TO_AUDIO->getJob(),
            'order' => 1,
            'process_id' => $processId,
            'meta->voice_id' => $voiceId,
            'meta->input_text' => 'some text here',
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });
})->group('repositories');
