<?php

namespace Tests\Unit\Jobs;

use App\Enums\DataType;
use App\Jobs\EmbedSource;
use App\Jobs\ExtractAndEmbedAudio;
use App\Models\Document;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ExtractAndEmbedAudioTest extends TestCase
{
    public function testJobExecution()
    {
        Bus::fake([EmbedSource::class]);
        $document = Document::factory()->create();

        // Create a mock MediaRepository instance
        $mediaRepo = $this->createMock(MediaRepository::class);
        $mediaRepo->expects($this->once())
            ->method('downloadYoutubeAudio')
            ->willReturn([
                'subtitles' => null,
                'total_duration' => 100,
                'file_paths' => ['path/to/audio.mp3'],
                'title' => 'Sample Audio'
            ]);
        $mediaRepo->expects($this->once())
            ->method('transcribeAudio')
            ->with(['path/to/audio.mp3'])
            ->willReturn('Transcribed Text');

        $job = new ExtractAndEmbedAudio($document, [
            'source_url' => 'https://example.com/audio',
            'collection_name' => 'sample_collection'
        ]);
        $job->mediaRepo = $mediaRepo;

        // Execute the job
        $job->handle();
        Bus::assertDispatchedSync(EmbedSource::class, function ($job) {
            return $job->dataType === DataType::TEXT
                && $job->meta['source'] === 'Title: Sample AudioContent: Transcribed Text'
                && $job->collectionName === 'sample_collection';
        });
    }
}
