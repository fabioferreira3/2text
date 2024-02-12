<?php

use App\Events\ContentBlockUpdated;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Jobs\RewriteTextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\DocumentTask;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->processId = Str::uuid();
    $this->contentBlock = DocumentContentBlock::factory()->create([
        'document_id' => $this->document->id,
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
    ]);
    $this->meta = [
        'document_content_block_id' => $this->contentBlock->id,
        'process_id' => $this->processId,
        'task_id' => $this->documentTask->id,
    ];

    $this->genRepoMock = Mockery::mock(GenRepository::class);
    $this->app->instance(GenRepository::class, $this->genRepoMock);

    Event::fake();
});

describe(
    'RewriteTextBlock job',
    function () {
        it('successfully rewrites text block', function () {
            expect($this->documentTask->fresh()->status)->toBe('in_progress');
            $this->genRepoMock->shouldReceive('rewriteTextBlock')
                ->once()
                ->with(Mockery::on(function ($arg) {
                    return $arg->id === $this->contentBlock->id;
                }), $this->meta)
                ->andReturn(['content' => 'Rewritten content', 'token_usage' => ['tokens' => 100]]);

            $job = new RewriteTextBlock($this->document, $this->meta);
            expect($job->uniqueId())->toBe('rewrite_text_block_' . $this->processId);
            $job->handle();

            expect($this->documentTask->fresh()->status)->toBe('finished');

            Bus::assertDispatched(RegisterUnitsConsumption::class, function ($job) {
                return $job->account->id === $this->document->account_id
                    && $job->type === 'words_generation'
                    && $job->meta['word_count'] === 2;
            });
            Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
                return $job->account->id === $this->document->account_id
                    && $job->params['tokens'] === 100
                    && $job->params['meta']['document_id'] === $this->document->id
                    && $job->params['meta']['document_task_id'] === $this->documentTask->id
                    && $job->params['meta']['name'] === 'rewrite_text_block';
            });
            Event::assertDispatched(ContentBlockUpdated::class, function ($event) {
                return $event->contentBlock->id === $this->contentBlock->id
                    && $event->processId === $this->processId->toString();
            });
        });

        it('handles http exceptions on rewrite failure and fails the job', function ($errorCode) {
            expect($this->documentTask->fresh()->status)->toBe('in_progress');
            $this->genRepoMock->shouldReceive('rewriteTextBlock')
                ->once()
                ->andThrow(new HttpException($errorCode, 'Failed to rewrite text block'));

            $job = new RewriteTextBlock($this->document, $this->meta);
            $job->handle();

            expect($this->documentTask->fresh()->status)->toBe('failed');

            Bus::assertNotDispatched(RegisterUnitsConsumption::class);
            Bus::assertNotDispatched(RegisterAppUsage::class);
            Event::assertNotDispatched(ContentBlockUpdated::class);
        })->with([400, 404, 422, 500, 502]);

        it('handles timeout exceptions and doesnt fail the job', function () {
            expect($this->documentTask->fresh()->status)->toBe('in_progress');

            $this->genRepoMock->shouldReceive('rewriteTextBlock')
                ->once()
                ->andThrow(new HttpException(504, 'Timeout'));

            $job = new RewriteTextBlock($this->document, $this->meta);
            $job->handle();

            expect($this->documentTask->fresh()->status)->toBe('in_progress');
        });

        it('has correct default values', function () {
            $job = new RewriteTextBlock($this->document, $this->meta);

            expect($job->tries)->toBe(10);
            expect($job->maxExceptions)->toBe(10);
        });
    }
);
