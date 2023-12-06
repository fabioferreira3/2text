<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Enums\AIModel;
use App\Models\User;
use App\Packages\OpenAI\ChatGPT;
use App\Packages\OpenAI\DallE;
use App\Packages\Oraculum\Oraculum;
use App\Repositories\GenRepository;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

uses()->beforeEach(function () {

    Bus::fake();
    Storage::fake('s3');
    $this->authUser = User::factory()->create();

    // ChatGPT Mock
    $this->chatGptRequestResponse = [
        'content' => 'AI content generated',
        'token_usage' => [
            'model' => AIModel::GPT_4_TURBO->value,
            'prompt' => 150,
            'completion' => 200,
            'total' => 350
        ]
    ];
    $this->chatGpt = Mockery::mock(ChatGPT::class);
    $this->chatGpt->shouldReceive('countTokens')->andReturn(500);

    $this->chatGpt->shouldReceive('request')->withArgs(function ($arg) {
        return is_array($arg);
    })->andReturn($this->chatGptRequestResponse);

    // Dall-E Mock
    $this->dallE = Mockery::mock(DallE::class);
    $this->dallE->shouldReceive('request')->withArgs(function ($arg) {
        return isset($arg['prompt'])
            && isset($arg['quality'])
            && isset($arg['n'])
            && is_string($arg['prompt'])
            && is_string($arg['quality'])
            && is_int($arg['n']);
    })->andReturn([
        'fileName' => 'file_name', 'imageData' => 'binary data'
    ]);

    // Oraculum
    $this->oraculum = Mockery::mock(new Oraculum($this->authUser, '12345'));
    $this->oraculum->shouldReceive('createBot')->andReturn([]);
    $this->oraculum->shouldReceive('add')->andReturn([]);
    $this->oraculum->shouldReceive('query')->andReturn([]);
    $this->oraculum->shouldReceive('chat')->andReturn([]);
    $this->oraculum->shouldReceive('deleteCollection')->andReturn([]);
    $this->oraculum->shouldReceive('countTokens')->andReturn([]);

    // Generator
    $this->generator = Mockery::mock(GenRepository::class);

    $this->generator->shouldReceive('generateSummary')->andReturn($this->chatGpt->request(['message']));
    $this->generator->shouldReceive('generateEmbeddedSummary')->andReturn($this->chatGpt->request(['message']));

    $this->generator->shouldReceive('generateSocialMediaPost')->andReturn($this->chatGpt->request(['message']));
    $this->generator->shouldReceive('generateEmbeddedSocialMediaPost')->andReturn($this->chatGpt->request(['message']));

    // Media Repository
    $this->mediaRepo = Mockery::mock(MediaRepository::class);
    $this->mediaRepo->shouldReceive('transcribeAudio')->andReturn("Transcribed text");
    $this->mediaRepo->shouldReceive('transcribeAudioWithDiarization');
})->in(__DIR__);

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
