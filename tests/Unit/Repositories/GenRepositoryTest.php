<?php

use App\Jobs\RegisterProductUsage;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Bus;

describe('GenRepository', function () {
    it('generates a title', function () {
        Bus::fake(RegisterProductUsage::class);
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $title = $repo->generateTitle($document, 'some context here');
        $expectedTitle = 'AI content generated';
        expect($title)->toBe($expectedTitle);
        expect($document->fresh()->title)->toBe($expectedTitle);
        expect($repo->response)->toBe($this->aiModelResponseResponse);

        Bus::assertDispatched(RegisterProductUsage::class);
    });

    it('generates an embedded title', function () {
        Bus::fake(RegisterProductUsage::class);
        $document = Document::factory()->create();
        $repo = new GenRepository();
        $title = $repo->generateEmbeddedTitle($document, 'collection name');
        $expectedTitle = 'AI content generated';
        expect($title)->toBe($expectedTitle);
        expect($document->fresh()->title)->toBe($expectedTitle);
        expect($repo->response)->toBe($this->aiModelResponseResponse);

        Bus::assertDispatched(RegisterProductUsage::class);
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
        Bus::fake(RegisterProductUsage::class);
        $documentContentBlock = DocumentContentBlock::factory()->create();
        $repo = new GenRepository();
        $response = $repo->rewriteTextBlock($documentContentBlock, ['prompt' => 'some prompt']);
        expect($response)->toBe('AI content generated');
        expect($documentContentBlock->fresh()->content)->toBe('AI content generated');
        Bus::assertDispatched(RegisterProductUsage::class);
    });
})->group('repositories');
