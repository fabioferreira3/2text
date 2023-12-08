<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Blog\CreateOutline;
use App\Jobs\RegisterProductUsage;
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
        it('generates the outline and parses the raw structure', function ($queryEmbedding) {
            Bus::fake(RegisterProductUsage::class);

            $mockOraculumFactory = Mockery::mock(OraculumFactoryInterface::class);
            $mockOraculumFactory->shouldReceive('make')->andReturn($this->oraculum);

            $mockChatGPTFactory = Mockery::mock(ChatGPTFactoryInterface::class);
            $mockChatGPTFactory->shouldReceive('make')->andReturn($this->chatGpt);

            $this->app->instance(OraculumFactoryInterface::class, $mockOraculumFactory);
            $this->app->instance(ChatGPTFactoryInterface::class, $mockChatGPTFactory);

            $processId = Str::uuid();
            $job = new CreateOutline(
                $this->document,
                [
                    'process_id' => $processId,
                    'query_embedding' => $queryEmbedding,
                    'collection_name' => $this->document->id,
                ]
            );
            $job->oraculumFactory = $mockOraculumFactory;
            $job->chatGptFactory = $mockChatGPTFactory;
            $job->handle();

            $this->document->refresh();

            expect($this->document->getMeta('outline'))->toBe($this->aiModelResponseResponse['content']);
            expect($this->document->getMeta('raw_structure'))->toBeArray();

            Bus::assertDispatched(RegisterProductUsage::class);
        })->with([true, false]);
    }
);
