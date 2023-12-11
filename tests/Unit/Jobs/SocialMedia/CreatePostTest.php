<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\RegisterProductUsage;
use App\Jobs\SocialMedia\CreatePost;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([RegisterProductUsage::class]);
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

    Bus::assertDispatched(RegisterProductUsage::class, function ($job) use ($document, $chatGptResponse) {
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
    it('calls the embeding function', function ($withTask) {

        $user = User::factory()->create();
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'max_words_count' => 250,
                'context' => fake()->words(1100, true),
                'user_id' => $user->id
            ]
        ]);
        $documentTask = null;

        if ($withTask) {
            $documentTask = DocumentTask::factory()->create([
                'document_id' => $document->id,
                'name' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->value,
                'job' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->getJob(),
                'order' => 1
            ]);
        }

        $job = new CreatePost($document, [
            'query_embedding' => true,
            'platform' => 'Facebook',
            'collection_name' => $document->id,
            'task_id' => $documentTask ? $documentTask->id : null
        ]);
        $job->handle();

        $chatGptResponse = $this->aiModelResponseResponse;
        commonCreateSocialPostAssertions($document, $chatGptResponse, $documentTask);
    })->with([true, false]);

    it('calls the non-embeding function', function ($withTask) {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'max_words_count' => 250,
                'context' => fake()->words(600, true),
                'user_id' => $user->id
            ]
        ]);

        $job = new CreatePost($document, [
            'query_embedding' => false,
            'platform' => 'Facebook'
        ]);
        $job->handle();

        $chatGptResponse = $this->aiModelResponseResponse;
        commonCreateSocialPostAssertions($document, $chatGptResponse);
    })->with([true, false]);
})->group('socialmedia');
