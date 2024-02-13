<?php

use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Blog\ExpandOutline;
use App\Jobs\RegisterAppUsage;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Packages\OpenAI\ChatGPT;
use App\Packages\Oraculum\Oraculum;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id
    ]);
    $this->firstPass = '<h2>First subtopic here</h2>\n<p>Hey there, music lover!</p><p>Other things here</p><h2>Another subtopic here</h2><p>And maybe some more here</p><p>A little bit there</p><p>And finally here</p>';

    $this->oraculumMock = Mockery::mock(new Oraculum($this->authUser, '12345'));
    $mockOraculumFactory = Mockery::mock(OraculumFactoryInterface::class);
    $mockOraculumFactory->shouldReceive('make')->andReturn($this->oraculumMock);
    $this->app->instance(OraculumFactoryInterface::class, $mockOraculumFactory);

    $this->chatGptMock = Mockery::mock(new ChatGPT($this->authUser, '12345'));
    $mockChatGptFactory = Mockery::mock(ChatGPTFactoryInterface::class);
    $mockChatGptFactory->shouldReceive('make')->andReturn($this->chatGptMock);
    $this->app->instance(ChatGPTFactoryInterface::class, $mockChatGptFactory);
});

describe('Blog - ExpandOutline job', function () {
    it('can be serialized', function () {
        $job = new ExpandOutline($this->document, [
            'query_embedding' => true,
            'collection_name' => 'collection name'
        ]);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('handles the job with embed query', function () {
        $this->oraculumMock->shouldReceive('query')->once()->andReturn([
            'content' => $this->firstPass,
            'token_usage' => [
                'model' => 'oraculum_model',
                'prompt' => 150,
                'completion' => 200,
                'total' => 350
            ]
        ]);

        $job = new ExpandOutline($this->document, [
            'query_embedding' => true,
            'collection_name' => $this->document->id,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        expect($this->document->fresh()->getMeta('first_pass'))->toBe($this->firstPass);
        expect($this->document->fresh()->getMeta('raw_structure'))
            ->toBeArray()
            ->toBe([
                [
                    'subheader' => 'First subtopic here',
                    'content' => '\n<p>Hey there, music lover!</p><p>Other things here</p>'
                ],
                [
                    'subheader' => 'Another subtopic here',
                    'content' => '<p>And maybe some more here</p><p>A little bit there</p><p>And finally here</p>'
                ]
            ]);
        Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
            return $job->account->id === $this->document->account_id &&
                $job->params['model'] === 'oraculum_model';
        });

        expect($this->documentTask->fresh()->status)->toBe('finished');
    });

    it('handles exceptions with embed query', function ($statusCode) {
        $originalStatus = $this->documentTask->status;
        $this->oraculumMock->shouldReceive('query')->once()->andThrow(new HttpException($statusCode, 'Error'));

        $job = new ExpandOutline($this->document, [
            'query_embedding' => true,
            'collection_name' => $this->document->id,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        Bus::assertNotDispatched(RegisterAppUsage::class);

        $expectedStatus = $statusCode !== 504 ? 'failed' : $originalStatus;
        expect($this->documentTask->fresh()->status)->toBe($expectedStatus);
    })->with([403, 422, 500, 504]);

    it('handles the job with gpt', function () {
        $this->chatGptMock->shouldReceive('request')->once()->andReturn([
            'content' => $this->firstPass,
            'token_usage' => [
                'model' => 'gpt_model',
                'prompt' => 150,
                'completion' => 200,
                'total' => 350
            ]
        ]);

        $job = new ExpandOutline($this->document, [
            'query_embedding' => false,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        expect($this->document->fresh()->getMeta('first_pass'))->toBe($this->firstPass);
        expect($this->document->fresh()->getMeta('raw_structure'))
            ->toBeArray()
            ->toBe([
                [
                    'subheader' => 'First subtopic here',
                    'content' => '\n<p>Hey there, music lover!</p><p>Other things here</p>'
                ],
                [
                    'subheader' => 'Another subtopic here',
                    'content' => '<p>And maybe some more here</p><p>A little bit there</p><p>And finally here</p>'
                ]
            ]);
        Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
            return $job->account->id === $this->document->account_id &&
                $job->params['model'] === 'gpt_model';
        });

        expect($this->documentTask->fresh()->status)->toBe('finished');
    });

    it('handles exceptions with gpt', function ($statusCode) {
        $originalStatus = $this->documentTask->status;
        $this->chatGptMock->shouldReceive('request')->once()->andThrow(new HttpException($statusCode, 'Error'));

        $job = new ExpandOutline($this->document, [
            'query_embedding' => false,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        Bus::assertNotDispatched(RegisterAppUsage::class);

        $expectedStatus = $statusCode !== 504 ? 'failed' : $originalStatus;
        expect($this->documentTask->fresh()->status)->toBe($expectedStatus);
    })->with([403, 422, 500, 504]);

    it('handles general exceptions', function ($queryEmbed) {
        if ($queryEmbed) {
            $this->oraculumMock->shouldReceive('query')->once()->andThrow(new Exception('Error'));
        } else {
            $this->chatGptMock->shouldReceive('request')->andThrow(new Exception('Error'));
        }

        $job = new ExpandOutline($this->document, [
            'query_embedding' => $queryEmbed,
            'collection_name' => 'some collection name',
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        Bus::assertNotDispatched(RegisterAppUsage::class);

        expect($this->documentTask->fresh()->status)->toBe('failed');
    })->with([true, false]);

    it('sets up with correct data', function () {
        $job = new ExpandOutline($this->document, [
            'query_embedding' => true,
            'collection_name' => $this->document->id,
            'task_id' => $this->documentTask->id
        ]);
        expect($job->backOff())->toBeArray()->toBe([5, 10, 15]);
        expect($job->tries)->toBe(10);
        expect($job->maxExceptions)->toBe(10);

        $retryUntil = $job->retryUntil();
        $expectedTime = now()->addMinutes(5);
        expect($retryUntil->diffInSeconds($expectedTime))->toBeLessThan(5);
    });

    it('generates the correct unique id', function () {
        $job = new ExpandOutline($this->document, [
            'query_embedding' => true,
            'collection_name' => $this->document->id,
            'task_id' => $this->documentTask->id
        ]);
        expect($job->uniqueId())->toBe('expand_outline_' . $this->document->id);
    });
})->group('blog');
