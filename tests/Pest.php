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
use App\Interfaces\AssemblyAIFactoryInterface;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Interfaces\WhisperFactoryInterface;
use App\Models\User;
use App\Packages\AssemblyAI\AssemblyAI;
use App\Packages\OpenAI\ChatGPT;
use App\Packages\OpenAI\DallE;
use App\Packages\Oraculum\Oraculum;
use App\Packages\Whisper\Whisper;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

uses()->beforeEach(function () {
    $this->withoutVite();
    Bus::fake();
    Storage::fake('s3');
    Storage::fake('local');
    Storage::fake('tmp-for-tests');
    $this->authUser = User::factory()->create();

    // AI Response Mock
    $this->aiModelResponseResponse = [
        'content' => 'AI content generated',
        'token_usage' => [
            'model' => AIModel::GPT_4_TURBO->value,
            'prompt' => 150,
            'completion' => 200,
            'total' => 350
        ]
    ];

    // ChatGPT Mock
    $this->chatGpt = Mockery::mock(ChatGPT::class);
    $this->chatGpt->shouldReceive('countTokens')->andReturn(500);

    $this->chatGpt->shouldReceive('request')->withArgs(function ($arg) {
        return is_array($arg);
    })->andReturn($this->aiModelResponseResponse);

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

    // Whisper Mock
    $this->whisper = Mockery::mock(Whisper::class);
    $this->whisper->shouldReceive('request')->andReturn([
        'text' => 'Transcribed text'
    ]);

    // Assembly AI Mock
    $this->assemblyAI = Mockery::mock(AssemblyAI::class);
    $this->assemblyAI->shouldReceive('transcribe')->andReturn('Transcribed text');

    // Oraculum Mock
    $this->oraculum = Mockery::mock(new Oraculum($this->authUser, '12345'));
    $this->oraculum->shouldReceive('createBot')->andReturn([]);
    $this->oraculum->shouldReceive('add')->andReturn([]);
    $this->oraculum->shouldReceive('query')->andReturn($this->aiModelResponseResponse);
    $this->oraculum->shouldReceive('chat')->andReturn([]);
    $this->oraculum->shouldReceive('deleteCollection')->andReturn([]);
    $this->oraculum->shouldReceive('countTokens')->andReturn([]);

    // Factories
    $this->mockOraculumFactory = Mockery::mock(OraculumFactoryInterface::class);
    $this->mockOraculumFactory->shouldReceive('make')->andReturn($this->oraculum);

    $this->mockChatGPTFactory = Mockery::mock(ChatGPTFactoryInterface::class);
    $this->mockChatGPTFactory->shouldReceive('make')->andReturn($this->chatGpt);

    $this->mockWhisperFactory = Mockery::mock(WhisperFactoryInterface::class);
    $this->mockWhisperFactory->shouldReceive('make')->andReturn($this->whisper);

    $this->mockAssemblyAIFactory = Mockery::mock(AssemblyAIFactoryInterface::class);
    $this->mockAssemblyAIFactory->shouldReceive('make')->andReturn($this->assemblyAI);

    $this->app->instance(OraculumFactoryInterface::class, $this->mockOraculumFactory);
    $this->app->instance(ChatGPTFactoryInterface::class, $this->mockChatGPTFactory);
    $this->app->instance(WhisperFactoryInterface::class, $this->mockWhisperFactory);
    $this->app->instance(AssemblyAIFactoryInterface::class, $this->mockAssemblyAIFactory);
})->in(__DIR__);

uses()->afterAll(function () {
    Storage::fake('s3');
    Storage::fake('local');
    Storage::fake('tmp-for-tests');
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
