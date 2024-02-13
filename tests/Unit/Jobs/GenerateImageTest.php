<?php

use App\Events\ImageNotGenerated;
use App\Jobs\GenerateImage;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Packages\OpenAI\Exceptions\ImageGenerationException;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
        'process_id' => Str::uuid()
    ]);
    $this->meta = [
        'process_id' => $this->documentTask->process_id,
        'task_id' => $this->documentTask->id,
    ];
});

describe('GenerateImage job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create();
        $job = new GenerateImage($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('sets up with correct data', function () {
        $job = new GenerateImage($this->document, $this->meta);
        expect($job->queue)->toBe('image_generation');
        expect($job->backOff())->toBeArray()->toBe([5, 10, 15]);
        expect($job->tries)->toBe(10);
        expect($job->maxExceptions)->toBe(10);

        $retryUntil = $job->retryUntil();
        $expectedTime = now()->addMinutes(5);
        expect($retryUntil->diffInSeconds($expectedTime))->toBeLessThan(5);
    });

    it('executes the job successfully', function () {
        $genRepoMock = Mockery::mock(GenRepository::class);
        $genRepoMock->shouldReceive('generateImage')->once();

        $this->app->instance(GenRepository::class, $genRepoMock);

        $job = new GenerateImage($this->document, $this->meta);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('finished');
    });

    it('handles http exception', function () {
        $genRepoMock = Mockery::mock(GenRepository::class);
        $genRepoMock->shouldReceive('generateImage')->once()
            ->andThrow(new HttpException(500, 'Failed to generate image'));

        $this->app->instance(GenRepository::class, $genRepoMock);

        $job = new GenerateImage($this->document, $this->meta);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('failed');
    });

    it('handles http 504 exception', function () {
        $originalTaskStatus = $this->documentTask->status;
        $genRepoMock = Mockery::mock(GenRepository::class);
        $genRepoMock->shouldReceive('generateImage')->once()
            ->andThrow(new HttpException(504, 'Failed to generate image'));

        $this->app->instance(GenRepository::class, $genRepoMock);

        $job = new GenerateImage($this->document, $this->meta);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe($originalTaskStatus);
    });

    it('handles image generation exception', function () {
        Event::fake();

        $genRepoMock = Mockery::mock(GenRepository::class);
        $genRepoMock->shouldReceive('generateImage')->once()
            ->andThrow(new ImageGenerationException('Failed to generate image'));

        $this->app->instance(GenRepository::class, $genRepoMock);

        $job = new GenerateImage($this->document, $this->meta);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('skipped');
        Event::assertDispatched(ImageNotGenerated::class, function ($event) {
            return $event->documentTask->id === $this->documentTask->id;
        });
    });

    it('generates the correct unique id', function () {
        $job = new GenerateImage($this->document, $this->meta);
        expect($job->uniqueId())->toBe('generate_image_' . $this->document->id);
    });
});
