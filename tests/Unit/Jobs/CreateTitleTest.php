<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Events\TitleGenerated;
use App\Jobs\CreateTitle;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->genRepoMock = Mockery::mock(GenRepository::class);
    $this->app->instance(GenRepository::class, $this->genRepoMock);
    $this->document = Document::factory()->create([
        'meta' => [
            'source' => SourceProvider::FREE_TEXT->value,
            'user_id' => $this->authUser->id
        ]
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
    ]);
});

function assertRegisterUnitsConsumption($context)
{
    Bus::assertDispatched(RegisterUnitsConsumption::class, function ($job) use ($context) {
        return $job->account->id === $context->document->account->id &&
            $job->type === 'words_generation' &&
            $job->meta['document_id'] === $context->document->id &&
            $job->meta['word_count'] === 3 &&
            $job->meta['job'] === DocumentTaskEnum::CREATE_TITLE->value;
    });
}

function assertRegisterAppUsage($context)
{
    Bus::assertDispatched(RegisterAppUsage::class, function ($job) use ($context) {
        return $job->account->id === $context->document->account->id &&
            $job->params['meta']['document_id'] === $context->document->id &&
            $job->params['meta']['document_task_id'] === $context->documentTask->id &&
            $job->params['meta']['name'] === DocumentTaskEnum::CREATE_TITLE->value;
    });
}

function assertEnding($context)
{
    Event::assertDispatched(TitleGenerated::class, function ($event) use ($context) {
        return $event->document->id === $context->document->id &&
            $event->processId === (string) $context->documentTask->process_id;
    });

    expect($context->documentTask->fresh()->status === 'finished');
}

describe('CreateTitle job', function () {
    it('setup the job', function () {
        $job = new CreateTitle($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();

        expect($job->uniqueId())->toBe('create_title_' . $this->document->id);
        expect($job->backOff())->toBeArray()->toBe([5, 10, 15]);
        expect($job->tries)->toBe(5);
        expect($job->maxExceptions)->toBe(5);

        $retryUntil = $job->retryUntil();
        $expectedTime = now()->addMinutes(2);
        expect($retryUntil->diffInSeconds($expectedTime))->toBeLessThan(2);
    });

    it('registers create title task with embedding', function () {
        Event::fake();
        $this->genRepoMock->shouldReceive('generateEmbeddedTitle')->andReturn($this->aiModelResponseResponse);
        $job = new CreateTitle(
            $this->document,
            [
                'query_embedding' => true,
                'collection_name' => $this->document->id,
                'task_id' => $this->documentTask->id,
                'process_id' => $this->documentTask->process_id
            ]
        );
        $job->handle();

        assertRegisterUnitsConsumption($this);
        assertRegisterAppUsage($this);
        assertEnding($this);
    });

    it('registers create title task without embedding', function () {
        Event::fake();
        $this->genRepoMock->shouldReceive('generateTitle')->andReturn($this->aiModelResponseResponse);
        $job = new CreateTitle(
            $this->document,
            [
                'query_embedding' => false,
                'text' => 'Test text for title generation',
                'task_id' => $this->documentTask->id,
                'process_id' => $this->documentTask->process_id
            ]
        );
        $job->handle();

        assertRegisterUnitsConsumption($this);
        assertRegisterAppUsage($this);
        assertEnding($this);
    });

    it('it handles http exceptions', function ($statusCode) {
        $this->genRepoMock->shouldReceive('generateTitle')->andThrow(new HttpException($statusCode));
        $job = new CreateTitle(
            $this->document,
            [
                'query_embedding' => false,
                'text' => 'Test text for title generation',
                'task_id' => $this->documentTask->id,
                'process_id' => $this->documentTask->process_id
            ]
        );
        $job->handle();
        $this->assertEquals($this->documentTask->fresh()->status, $statusCode !== 504 ? 'failed' : 'ready');
    })->with([504, 500, 404, 422, 403]);

    it('it handles non-http exceptions', function () {
        $this->genRepoMock->shouldReceive('generateTitle')->andThrow(new Exception());

        $job = new CreateTitle(
            $this->document,
            [
                'query_embedding' => false,
                'text' => 'Test text for title generation',
                'task_id' => $this->documentTask->id,
                'process_id' => $this->documentTask->process_id
            ]
        );
        $job->handle();

        $this->assertEquals($this->documentTask->fresh()->status, 'aborted');
    });
});
