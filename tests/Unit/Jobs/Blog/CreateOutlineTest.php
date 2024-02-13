<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\Blog\CreateOutline;
use App\Jobs\RegisterAppUsage;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'user_id' => User::factory()->create()->id
        ]
    ]);
});

describe(
    'Blog - CreateOutline job',
    function () {
        it('can be serialized', function () {
            $job = new CreateOutline(
                $this->document,
                [
                    'process_id' => Str::uuid(),
                    'query_embedding' => true,
                    'collection_name' => $this->document->id,
                ]
            );
            $serialized = serialize($job);
            expect($serialized)->toBeString();
        });

        it('generates the outline and parses the raw structure', function ($queryEmbedding) {
            Bus::fake(RegisterAppUsage::class);

            $processId = Str::uuid();
            $job = new CreateOutline(
                $this->document,
                [
                    'process_id' => $processId,
                    'query_embedding' => $queryEmbedding,
                    'collection_name' => $this->document->id,
                ]
            );
            $job->oraculumFactory = $this->mockOraculumFactory;
            $job->chatGptFactory = $this->mockChatGPTFactory;
            $job->handle();

            $this->document->refresh();

            expect($this->document->getMeta('outline'))->toBe($this->aiModelResponseResponse['content']);
            expect($this->document->getMeta('raw_structure'))->toBeArray();

            Bus::assertDispatched(RegisterAppUsage::class);
        })->with([true, false]);
    }
)->group('blog');
