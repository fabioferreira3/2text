<?php

use App\Enums\SourceProvider;
use App\Jobs\DownloadAudio;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Repositories\MediaRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->mediaRepoMock = Mockery::mock(MediaRepository::class);
    $this->app->instance(MediaRepository::class, $this->mediaRepoMock);
    $this->filePaths = [
        fake()->url(),
        fake()->url(),
        fake()->url(),
    ];
    $this->document = Document::factory()->create([
        'meta' => [
            'source' => SourceProvider::YOUTUBE->value,
            'user_id' => $this->authUser->id
        ]
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
    ]);
});

describe('DownloadAudio job', function () {
    it('setup the job', function () {
        $job = new DownloadAudio($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();

        expect($job->uniqueId())->toBe('download_audio_' . $this->document->id);
        expect($job->backOff())->toBeArray()->toBe([5, 10, 15]);
        expect($job->tries)->toBe(10);
        expect($job->maxExceptions)->toBe(10);
        expect($job->middleware())->toBeArray();

        $retryUntil = $job->retryUntil();
        $expectedTime = now()->addMinutes(5);
        expect($retryUntil->diffInSeconds($expectedTime))->toBeLessThan(5);
    });

    it('downloads the audio', function () {
        $this->mediaRepoMock->shouldReceive('downloadYoutubeAudio')->andReturn([
            'total_duration' => 100,
            'title' => 'Test title',
            'file_paths' => $this->filePaths
        ]);
        $job = new DownloadAudio(
            $this->document,
            [
                'source_url' => fake()->url(),
                'task_id' => $this->documentTask->id
            ]
        );
        $job->handle();

        $this->document->refresh();

        expect($this->document->title)->toBe('Test title');
        expect($this->document->meta['duration'])->toBe(100);
        expect($this->document->meta['audio_file_path'])->toBeArray()->toBe($this->filePaths);

        expect($this->documentTask->fresh()->status === 'finished');
    });

    it('it handles http exceptions', function ($statusCode) {
        $mediaRepoMock = Mockery::mock(MediaRepository::class);
        $this->app->instance(MediaRepository::class, $mediaRepoMock);
        $mediaRepoMock->shouldReceive('downloadYoutubeAudio')->once()->andThrow(new HttpException(504));

        $job = new DownloadAudio(
            $this->document,
            [
                'source_url' => fake()->url(),
                'task_id' => $this->documentTask->id
            ]
        );
        $job->handle();
        expect($this->documentTask->fresh()->status, $statusCode !== 504 ? 'failed' : 'in_progress');
    })->with([504, 500, 404, 422]);

    it('it handles non-http exceptions', function () {
        $mediaRepoMock = Mockery::mock(MediaRepository::class);
        $this->app->instance(MediaRepository::class, $mediaRepoMock);
        $mediaRepoMock->shouldReceive('downloadYoutubeAudio')->once()->andThrow(new Exception('Error'));

        $job = new DownloadAudio(
            $this->document,
            [
                'source_url' => fake()->url(),
                'task_id' => $this->documentTask->id
            ]
        );
        $job->handle();
        expect($this->documentTask->fresh()->status, 'aborted');
    });
});
