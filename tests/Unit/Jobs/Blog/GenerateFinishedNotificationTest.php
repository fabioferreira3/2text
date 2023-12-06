<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Helpers\PromptHelperFactory;
use App\Jobs\Blog\GenerateFinishedNotification;
use App\Models\Document;
use App\Models\User;
use App\Packages\OpenAI\ChatGPT;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'user_id' => $this->user->id
        ]
    ]);
});

describe(
    'Blog - GenerateFinishedNotification job',
    function () {
        it('generates the finished notification and updates the document', function () {
            $promptHelper = PromptHelperFactory::create($this->document->language->value);
            $chatGpt = Mockery::mock(ChatGPT::class);
            $chatGpt->shouldReceive('request')
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
            $job->chatGpt = $chatGpt;
            $job->handle();

            $this->document->refresh();

            expect($this->document->getMeta('finished_email_content'))->toBe($this->aiModelResponseResponse['content']);
        });
    }
);
