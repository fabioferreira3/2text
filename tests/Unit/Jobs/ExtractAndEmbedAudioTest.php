<?php

namespace Tests\Unit\Jobs;

use App\Enums\DataType;
use App\Jobs\EmbedSource;
use App\Jobs\ExtractAndEmbedAudio;
use App\Models\Document;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ExtractAndEmbedAudioTest extends TestCase
{
    public function testJobExecution()
    {
        // Create a mock Document instance
        $document = $this->createMock(Document::class);
        $document->expects($this->once())
            ->method('fresh');

        // Create a mock MediaRepository instance
        $mediaRepo = $this->createMock(MediaRepository::class);
        $mediaRepo->expects($this->once())
            ->method('downloadYoutubeAudio')
            ->willReturn([
                'file_paths' => ['path/to/audio.mp3'],
                'title' => 'Sample Audio'
            ]);
        $mediaRepo->expects($this->once())
            ->method('transcribeAudio')
            ->with(['path/to/audio.mp3'])
            ->willReturn('Transcribed Text');

        // Create a mock EmbedSource job
        $embedSourceJob = $this->createMock(EmbedSource::class);
        $embedSourceJob->expects($this->once())
            ->method('dispatchSync')
            ->with($document, [
                'data_type' => DataType::TEXT->value,
                'source' => 'Title: Sample AudioContent: Transcribed Text',
                'collection_name' => 'sample_collection'
            ]);

        // Mock the Log facade
        Log::shouldReceive('error')
            ->once();

        // Create an instance of the ExtractAndEmbedAudio job
        $job = new ExtractAndEmbedAudio($document, [
            'source_url' => 'https://example.com/audio',
            'collection_name' => 'sample_collection'
        ]);

        // Set the mock objects
        $job->mediaRepo = $mediaRepo;
        EmbedSource::$__instance = $embedSourceJob;

        // Execute the job
        $job->handle();
    }

    public function testJobFailure()
    {
        // Create a mock Document instance
        $document = $this->createMock(Document::class);
        $document->expects($this->once())
            ->method('fresh');

        // Create a mock MediaRepository instance
        $mediaRepo = $this->createMock(MediaRepository::class);
        $mediaRepo->expects($this->once())
            ->method('downloadYoutubeAudio')
            ->willThrowException(new Exception('Download failed'));

        // Mock the Log facade
        Log::shouldReceive('error')
            ->once();

        // Create an instance of the ExtractAndEmbedAudio job
        $job = new ExtractAndEmbedAudio($document, [
            'source_url' => 'https://example.com/audio',
            'collection_name' => 'sample_collection'
        ]);

        // Set the mock objects
        $job->mediaRepo = $mediaRepo;

        // Execute the job
        $job->handle();
    }
}
