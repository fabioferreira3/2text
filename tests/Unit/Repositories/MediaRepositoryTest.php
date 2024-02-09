<?php

use App\Enums\MediaType;
use App\Exceptions\DownloadYoutubeAudioException;
use App\Exceptions\DownloadYoutubeSubtitlesException;
use App\Interfaces\AssemblyAIFactoryInterface;
use App\Models\Account;
use App\Models\MediaFile;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use YoutubeDl\Entity\Video;
use YoutubeDl\Entity\VideoCollection;

beforeEach(function () {
    $this->mediaRepository = new MediaRepository();
    $this->mediaRepository->yt = $this->youtubeDlMock;
});

describe('MediaRepository', function () {
    it('can create a new image media file', function () {
        $accountMock = Mockery::mock(Account::class);
        $accountMock->shouldReceive('mediaFiles->save')->once()->andReturnUsing(function ($mediaFile) {
            expect($mediaFile)->toBeInstanceOf(MediaFile::class);
            expect($mediaFile->type)->toEqual(MediaType::IMAGE);
        });

        $fileParams = [
            'file_url' => 'http://example.com/image.jpg',
            'file_path' => 'images/image.jpg',
            'file_size' => 1024,
            'file_width' => 640,
            'file_height' => 480,
            'file_extension' => 'jpg',
            'file_public_id' => 'unique_public_id',
            'meta' => ['additional' => 'meta data'],
        ];

        $mediaFile = MediaRepository::newImage($accountMock, $fileParams);

        expect($mediaFile)->toBeInstanceOf(MediaFile::class);
        expect($mediaFile->file_url)->toEqual('http://example.com/image.jpg');
        expect($mediaFile->file_path)->toEqual('images/image.jpg');
        expect($mediaFile->type)->toEqual(MediaType::IMAGE);
        expect($mediaFile->meta)->toBeArray();
        expect($mediaFile->meta['size'])->toEqual(1024);
        expect($mediaFile->meta['width'])->toEqual(640);
        expect($mediaFile->meta['height'])->toEqual(480);
        expect($mediaFile->meta['extension'])->toEqual('jpg');
        expect($mediaFile->meta['publicId'])->toEqual('unique_public_id');
    });

    it('can store an image on s3', function () {
        $account = Account::factory()->create(); // Assuming Account factory exists
        $fileParams = [
            'fileName' => 'image.jpg',
            'imageData' => 'image_data_here', // Placeholder for actual image data
        ];

        Storage::fake('s3');

        MediaRepository::storeImage($account, $fileParams);

        Storage::disk('s3')->assertExists('ai-images/' . $fileParams['fileName']);
        // Since we're not testing the internals of optimizeAndStore here, ensure it's properly mocked if necessary
    });

    it('optimizes and stores an image, then returns a MediaFile instance', function () {
        $account = Account::factory()->create();

        $fileParams = [
            'fileName' => 'ai-images/image.jpg',
            'meta' => ['sample_meta_key' => 'sample_meta_value'],
        ];

        Storage::disk('s3')->put($fileParams['fileName'], 'image contents');

        $mediaFile = MediaRepository::optimizeAndStore($account, $fileParams);

        expect($mediaFile)->toBeInstanceOf(MediaFile::class);
        expect($mediaFile->file_url)->toEqual('https://cloudinary.com/secure_image_path.jpg');
    });

    it('downloads YouTube subtitles successfully', function () {
        $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $videoLanguage = 'en';

        $this->youtubeDlMock->shouldReceive('setBinPath')->once();
        $this->youtubeDlMock->shouldReceive('download')->once()->andReturn(new VideoCollection([new Video()]));

        $result = $this->mediaRepository->downloadYoutubeSubtitles($youtubeUrl, $videoLanguage);

        expect($result)->toBeArray();
        expect($result)->toHaveKeys(['subtitles', 'file_paths', 'title', 'total_duration']);
        expect($result['file_paths'])->toBeArray();
    })->skip(fn () => !app()->environment('testing'), 'Download test is only for testing environment');

    it('handles exceptions on downloading YouTube subtitles', function () {
        $youtubeUrl = 'invalid_url';
        $videoLanguage = 'en';

        $this->youtubeDlMock->shouldReceive('download')->andThrow(new Exception('Invalid URL'));

        $this->mediaRepository->downloadYoutubeSubtitles($youtubeUrl, $videoLanguage);
    })->throws(DownloadYoutubeSubtitlesException::class);

    it('downloads YouTube audio successfully', function () {
        $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $tempFilePath = tempnam(sys_get_temp_dir(), 'audio');
        file_put_contents($tempFilePath, 'test'); // Simulate audio content
        $tempFile = new SplFileInfo($tempFilePath);

        // Setup the mock to return a predefined video object
        $mockVideo = Mockery::mock(Video::class);
        $mockVideo->shouldReceive('getTitle')->andReturn('Sample Audio Title');
        $mockVideo->shouldReceive('getDuration')->andReturn(300);
        $mockVideo->shouldReceive('getFile')->andReturn($tempFile);
        $mockVideo->shouldReceive('getError')->andReturn(null);

        $this->youtubeDlMock->shouldReceive('setBinPath')->once();
        $this->youtubeDlMock->shouldReceive('download')->once()->andReturn(new VideoCollection([$mockVideo]));

        $result = $this->mediaRepository->downloadYoutubeAudio($youtubeUrl);

        expect($result)->toBeArray();
        expect($result)->toHaveKeys(['subtitles', 'file_paths', 'total_duration', 'title']);
        expect($result['subtitles'])->toBeNull();
        expect($result['file_paths'])->toBeArray();
        expect($result['title'])->toEqual('Sample Audio Title');
        expect($result['total_duration'])->toBeGreaterThanOrEqual(1);

        // Assert file was stored on S3
        foreach ($result['file_paths'] as $path) {
            Storage::disk('s3')->assertExists($path);
        }
    });

    it('handles exceptions on downloading YouTube audio', function () {
        $youtubeUrl = 'invalid_url';

        $this->youtubeDlMock->shouldReceive('setBinPath')->once();
        $this->youtubeDlMock->shouldReceive('download')->once()->andThrow(new Exception('Error downloading audio'));

        $this->mediaRepository->downloadYoutubeAudio($youtubeUrl);
    })->throws(DownloadYoutubeAudioException::class);

    it('transcribes audio files successfully', function () {
        $audioFilePaths = ['test_audio.mp3'];
        $expectedTranscription = "Transcribed text";

        // Setup Storage facade to simulate file existence on S3 and local storage
        Storage::fake('s3');
        Storage::fake('local');
        Storage::disk('s3')->put($audioFilePaths[0], 'contents of the audio file');

        $result = $this->mediaRepository->transcribeAudio($audioFilePaths);

        expect($result)->toEqual($expectedTranscription);

        // Assert that the original and any temporary files are cleaned up from local storage
        Storage::disk('local')->assertMissing($audioFilePaths[0]);
        Storage::disk('local')->assertMissing('compressed_' . $audioFilePaths[0]);
    });

    it('handles empty audio file paths array', function () {
        $audioFilePaths = [];

        $result = $this->mediaRepository->transcribeAudio($audioFilePaths);

        expect($result)->toBe('');
    });

    it('retrieves transcription subtitles', function () {
        $transcriptionId = 'test_transcription_id';
        $mockSubtitles = ['vtt' => 'VTT Content', 'srt' => 'SRT Content'];
        $this->assemblyAI->shouldReceive('getTranscriptionSubtitles')->once()->andReturn($mockSubtitles);

        $result = $this->mediaRepository->getTranscriptionSubtitles($transcriptionId);

        expect($result)->toHaveKeys(['srt_file_path', 'vtt_file_path']);
        Storage::disk('s3')->assertExists($result['srt_file_path']);
        Storage::disk('s3')->assertExists($result['vtt_file_path']);
    });

    it('retrieves transcription', function () {
        $transcriptionId = 'test_transcription_id';
        $expectedResult = ['text' => 'Transcribed text here'];
        $this->assemblyAI->shouldReceive('getTranscription')->once()->andReturn($expectedResult);

        $result = $this->mediaRepository->getTranscription($transcriptionId);

        expect($result)->toBe($expectedResult);
    });

    it('handles type errors when file is missing', function () {
        $youtubeUrl = 'https://www.youtube.com/watch?v=videoWithoutSubtitles';
        $videoLanguage = 'en';

        $this->youtubeDlMock->shouldReceive('setBinPath')->once();
        $this->youtubeDlMock->shouldReceive('download')->once()->andReturn(new VideoCollection([new Video()]));

        $result = $this->mediaRepository->downloadYoutubeSubtitles($youtubeUrl, $videoLanguage);
        expect($result['subtitles'])->toBeNull();
        expect($result['file_paths'])->toBeArray();
        expect($result['file_paths'])->toContain(null);
    });
})->group('repositories');
