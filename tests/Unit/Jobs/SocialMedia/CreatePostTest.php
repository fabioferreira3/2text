<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\RegisterAppUsage;
use App\Jobs\SocialMedia\CreatePost;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([RegisterAppUsage::class]);
    $this->user = User::factory()->create();
    $this->document = Document::factory()->create([
        'meta' => [
            'source' => SourceProvider::FREE_TEXT->value,
            'max_words_count' => 250,
            'context' => fake()->words(1100, true),
            'user_id' => $this->user->id
        ]
    ]);
});

function commonCreateSocialPostAssertions($document, $chatGptResponse, $documentTask = null)
{
    $contentBlock = $document->fresh()->contentBlocks()->first();
    expect($contentBlock)->toMatchArray([
        'document_id' => $document->id,
        'content' => 'AI content generated',
        'type' => 'text',
        'prompt' => null
    ]);

    Bus::assertDispatched(RegisterAppUsage::class, function ($job) use ($document, $chatGptResponse) {
        return $job->account->id === $document->account_id &&
            $job->params['model'] === $chatGptResponse['token_usage']['model'] &&
            $job->params['prompt'] === $chatGptResponse['token_usage']['prompt'] &&
            $job->params['completion'] === $chatGptResponse['token_usage']['completion'] &&
            $job->params['total'] === $chatGptResponse['token_usage']['total'] &&
            $job->params['meta']['document_id'] === $document->id;
    });

    if ($documentTask) {
        expect($documentTask->fresh()->status)->toBe(DocumentStatus::FINISHED->value);
    }
}

describe('Social Media - CreatePost job', function () {
    it('can be serialized', function () {
        $job = new CreatePost($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('calls the embeding function', function ($withTask) {
        $documentTask = null;

        if ($withTask) {
            $documentTask = DocumentTask::factory()->create([
                'document_id' => $this->document->id,
                'name' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->value,
                'job' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->getJob(),
                'order' => 1
            ]);
        }

        $job = new CreatePost($this->document, [
            'query_embedding' => true,
            'platform' => 'Facebook',
            'collection_name' => $this->document->id,
            'task_id' => $documentTask ? $documentTask->id : null
        ]);
        $job->handle();

        $chatGptResponse = $this->aiModelResponseResponse;
        commonCreateSocialPostAssertions($this->document, $chatGptResponse, $documentTask);
    })->with([true, false]);

    it('calls the non-embeding function', function () {
        $job = new CreatePost($this->document, [
            'query_embedding' => false,
            'platform' => 'Facebook'
        ]);
        $job->handle();

        $chatGptResponse = $this->aiModelResponseResponse;
        commonCreateSocialPostAssertions($this->document, $chatGptResponse);
    });
})->group('socialmedia');
