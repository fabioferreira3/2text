<?php

use App\Enums\SourceProvider;
use App\Jobs\RegisterProductUsage;
use App\Jobs\SocialMedia\CreatePost;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([RegisterProductUsage::class]);
});

function commonCreateSocialPostAssertions($document, $chatGptResponse)
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
}

describe('Social Media - CreatePost job', function () {
    it('calls the embeding function', function () {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'max_words_count' => 250,
                'context' => fake()->words(1100, true),
                'user_id' => $user->id
            ]
        ]);

        $job = new CreatePost($document, [
            'query_embedding' => true,
            'platform' => 'Facebook',
            'collection_name' => $document->id
        ], $this->generator);
        $job->handle();

        $chatGptResponse = $this->chatGptRequestResponse;
        commonCreateSocialPostAssertions($document, $chatGptResponse);
    });

    it('calls the non-embeding function', function () {
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
        ], $this->generator);
        $job->handle();

        $chatGptResponse = $this->chatGptRequestResponse;
        commonCreateSocialPostAssertions($document, $chatGptResponse);
    });
})->group('socialmedia');
