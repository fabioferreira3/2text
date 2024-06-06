<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Factories\LLMFactory;
use App\Helpers\PromptHelperFactory;
use App\Interfaces\LLMFactoryInterface;
use App\Jobs\Blog\GenerateFinishedNotification;
use App\Models\Document;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'user_id' => $this->user->id
        ]
    ]);
    $this->factoryInterface = Mockery::mock(LLMFactoryInterface::class);
    $llmFactory = Mockery::mock(LLMFactory::class);
    $llmFactory->shouldReceive('make')->andReturn($this->factoryInterface);
    $this->app->instance(LLMFactory::class, $llmFactory);
});

describe(
    'Blog - GenerateFinishedNotification job',
    function () {
        it('can be serialized', function () {
            $job = new GenerateFinishedNotification($this->document, []);
            $serialized = serialize($job);
            expect($serialized)->toBeString();
        });

        it('generates the finished notification and updates the document', function () {
            $promptHelper = PromptHelperFactory::create($this->document->language->value);
            $this->factoryInterface->shouldReceive('request')
                ->with([[
                    'role' => 'user',
                    'content' =>  $promptHelper->generateFinishedNotification([
                        'jobName' => $this->document->type->label(),
                        'context' => $this->document->getContext(),
                        'owner' => $this->user->name,
                        'document_link' => route('blog-post-view', ['document' => $this->document])
                    ])
                ]])
                ->andReturn($this->aiModelResponseResponse);
            $job = new GenerateFinishedNotification($this->document, []);
            $job->handle();

            $this->document->refresh();

            expect($this->document->getMeta('finished_email_content'))->toBe($this->aiModelResponseResponse['content']);
        });
    }
)->group('blog');
