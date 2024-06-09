<?php

namespace App\Repositories;

use App\Enums\MediaType;
use App\Exceptions\DownloadYoutubeAudioException;
use App\Exceptions\DownloadYoutubeSubtitlesException;
use App\Helpers\MediaHelper;
use App\Interfaces\AssemblyAIFactoryInterface;
use App\Interfaces\WhisperFactoryInterface;
use App\Models\Account;
use App\Models\MediaFile;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use TypeError;
use YoutubeDl\YoutubeDl;
use YoutubeDl\Options;

class MediaRepository
{
    public WhisperFactoryInterface $whisperFactory;
    public AssemblyAIFactoryInterface $assemblyAIFactory;
    public YoutubeDl $yt;
    public $mediaHelper;

    public function __construct()
    {
        $this->whisperFactory = app(WhisperFactoryInterface::class);
        $this->assemblyAIFactory = app(AssemblyAIFactoryInterface::class);
        $this->mediaHelper = new MediaHelper();
        $this->yt = new YoutubeDl();
    }

    public static function newImage(Account $account, array $fileParams): MediaFile
    {
        $mediaFile = new MediaFile([
            'file_url' => $fileParams['file_url'],
            'file_path' => $fileParams['file_path'],
            'type' => MediaType::IMAGE,
            'meta' => [
                'size' => $fileParams['file_size'],
                'width' => $fileParams['file_width'],
                'height' => $fileParams['file_height'],
                'extension' => $fileParams['file_extension'],
                'publicId' => $fileParams['file_public_id'],
                ...$fileParams['meta'] ?? []
            ]
        ]);
        $account->mediaFiles()->save($mediaFile);

        return $mediaFile;
    }

    public static function storeImage(Account $account, $fileParams): MediaFile
    {
        $fileParams['fileName'] = 'ai-images/' . $fileParams['fileName'];
        Storage::disk('s3')->put($fileParams['fileName'], $fileParams['imageData']);

        return self::optimizeAndStore($account, $fileParams);
    }

    public static function optimizeAndStore(Account $account, $fileParams): MediaFile
    {
        $originalFileUrl = Storage::temporaryUrl($fileParams['fileName'], now()->addMinutes(5));
        $uploadedFile = cloudinary()->upload($originalFileUrl);

        return self::newImage($account, [
            'file_url' => $uploadedFile->getSecurePath(),
            'file_path' => $fileParams['fileName'],
            'file_size' => $uploadedFile->getSize(),
            'file_width' => $uploadedFile->getWidth(),
            'file_height' => $uploadedFile->getHeight(),
            'file_extension' => $uploadedFile->getExtension(),
            'file_public_id' => $uploadedFile->getPublicId(),
            'meta' => [...$fileParams['meta'] ?? []]
        ]);
    }

    public function downloadYoutubeSubtitles($youtubeUrl, $videoLanguage = 'en')
    {
        try {
            if (app()->environment('production')) {
                $this->yt->setBinPath('/app/yt-dlp');
            } else {
                $this->yt->setBinPath('/usr/local/bin/yt-dlp');
            }

            $collection = $this->yt->download(
                Options::create()
                    ->downloadPath(storage_path('app'))
                    ->skipDownload(true)
                    ->subLang([$videoLanguage])
                    ->writeSub(true)
                    ->writeAutoSub(true)
                    ->subFormat('vtt')
                    ->url($youtubeUrl)
            )->getVideos();

            $videoTitle = null;
            $file = null;
            $transcription = null;
            $duration = null;
            $subtitleFilePaths = [];

            foreach ($collection as $video) {
                if ($video->getError() !== null) {
                    throw new Exception("Error downloading video: {$video->getError()}.");
                } else {
                    try {
                        $file = $video->getFile();
                    } catch (TypeError $e) {
                    }
                    $duration = ceil($video->getDuration() / 60);
                    $videoTitle = $video->getTitle();
                    $transcription = $file ?
                        $this->mediaHelper->convertWebVttToPlainText(file_get_contents($file->getPathname())) : null;
                    $subtitleFilePaths[] = $file ? $file->getBasename() : null;
                }
            }

            return [
                'subtitles' => $transcription,
                'file_paths' => $subtitleFilePaths,
                'title' => $videoTitle,
                'total_duration' => $duration
            ];
        } catch (Exception $e) {
            throw new DownloadYoutubeSubtitlesException();
        }
    }

    public function downloadYoutubeAudio($youtubeUrl)
    {
        try {
            if (app()->environment('production')) {
                $this->yt->setBinPath('/app/yt-dlp');
            } else {
                $this->yt->setBinPath('/usr/local/bin/yt-dlp');
            }

            $fileName = Str::uuid() . '.%(ext)s';

            $collection = $this->yt->download(
                Options::create()
                    ->downloadPath(storage_path('app'))
                    ->extractAudio(true)
                    ->audioFormat('mp3')
                    ->audioQuality('4') // 0 = best
                    ->output($fileName)
                    ->url($youtubeUrl)
            )->getVideos();

            $downloadedVideo = null;
            $videoTitle = null;
            foreach ($collection as $video) {
                if ($video->getError() !== null) {
                    throw new Exception("Error downloading video: {$video->getError()}.");
                } else {
                    $downloadedVideo = $video;
                    $videoTitle = $video->getTitle();
                }
            }

            if (!$downloadedVideo) {
                throw new Exception("Another error I do not understand");
            }

            $duration = ceil($downloadedVideo->getDuration() / 60);
            $localFilePath = $downloadedVideo->getFile();

            $fileSize = $localFilePath->getSize();
            $maxSize = 23 * 1024 * 1024; // 25MB in bytes

            $audioFilePaths = [];

            if ($fileSize > $maxSize) {
                // Get the duration of the MP3 file in seconds using ffmpeg
                $durationCommand = "ffmpeg -i {$localFilePath} 2>&1 | grep Duration | awk '{print $2}' | tr -d ,";
                $durationString = shell_exec($durationCommand);
                list($hours, $minutes, $seconds) = sscanf($durationString, "%d:%d:%d");
                $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;

                // Determine chunk duration in seconds to approximate 25MB
                $chunkDuration = floor(($maxSize / $fileSize) * $totalSeconds);

                $partNumber = 0;
                for ($start = 0; $start < $totalSeconds; $start += $chunkDuration) {
                    $outputFile = "{$localFilePath}_part_" . sprintf('%03d', $partNumber) . ".mp3";
                    $ffmpegCommand = "ffmpeg -i {$localFilePath} -ss {$start} -t {$chunkDuration} -c copy {$outputFile}";
                    exec($ffmpegCommand);

                    $basename = basename($outputFile);
                    Storage::disk('s3')->put($basename, file_get_contents($outputFile));
                    $audioFilePaths[] = $basename;

                    // Delete the local split part after uploading to S3
                    @unlink($outputFile);
                    $partNumber++;
                }
                // Delete the original large file as well
                @unlink($localFilePath);
            } else {
                // If not exceeding, just add the single path to our paths array
                Storage::disk('s3')->put($downloadedVideo->getFile()->getBasename(), file_get_contents($localFilePath));
                $audioFilePaths[] = $collection[0]->getFile()->getBasename();
            }

            return [
                'subtitles' => null,
                'file_paths' => $audioFilePaths,
                'total_duration' => $duration,
                'title' => $videoTitle
            ];
        } catch (Exception $e) {
            throw new DownloadYoutubeAudioException($e->getMessage());
        }
    }

    public function transcribeAudio(array $audioFilePaths)
    {
        $transcription = collect([]);
        if (count($audioFilePaths)) {
            foreach ($audioFilePaths as $audioFilePath) {
                // Get the file from S3 and store it locally if it doesn't exist
                if (!Storage::disk('local')->exists($audioFilePath)) {
                    $fileUrl = Storage::disk('s3')->get($audioFilePath);
                    Storage::disk('local')->put($audioFilePath, $fileUrl);
                }

                // Check the file size and compress it if necessary
                $localFilePath = Storage::disk('local')->path($audioFilePath);
                $neededToCompress = self::compressAndStoreMp3(
                    new \Illuminate\Http\File($localFilePath),
                    $audioFilePath
                );

                $whisper = $this->whisperFactory->make($localFilePath);
                $response = $whisper->request();

                // Clean up the local files
                Storage::disk('local')->delete($audioFilePath);
                if ($neededToCompress) {
                    Storage::disk('local')->delete('compressed_' . $audioFilePath);
                }

                $transcription->push($response['text']);
            }
        }

        return json_decode('"' . $transcription->implode(' ') . '"');
    }

    public function mergeAudioFiles($audioFilePaths)
    {
        $localTempDir = storage_path('app/temp_audio_merge_' . uniqid());
        mkdir($localTempDir, 0777, true);

        // Temporary local file for the merged audio
        $localMergedFilePath = $localTempDir . '/merged_audio.mp3';

        // Build the ffmpeg command to merge files
        $ffmpegCommand = 'ffmpeg -y';
        foreach ($audioFilePaths as $filePath) {
            // Download each file from S3 to the local temporary directory
            $localFilePath = $localTempDir . '/' . basename($filePath);
            Storage::disk('s3')->getDriver()->readStream($filePath, 'r+');
            file_put_contents($localFilePath, Storage::disk('s3')->get($filePath));

            $ffmpegCommand .= ' -i ' . escapeshellarg($localFilePath);
        }
        $ffmpegCommand .= ' -filter_complex \'concat=n=' . count($audioFilePaths) . ':v=0:a=1\' ' . escapeshellarg($localMergedFilePath);

        // Execute the ffmpeg command
        exec($ffmpegCommand);

        // Upload the merged file to S3
        $mergedFilePath = 'ai-audio/' . Str::uuid() . '.mp3';
        Storage::disk('s3')->put($mergedFilePath, file_get_contents($localMergedFilePath));

        array_map('unlink', glob("$localTempDir/*.*"));
        rmdir($localTempDir);

        // Generate a temporary URL for the merged file on S3
        return Storage::temporaryUrl($mergedFilePath, now()->addMinutes(15));
    }

    public function transcribeAudioWithDiarization(array $audioFilePaths, array $params = [])
    {
        $tempUrl = count($audioFilePaths) > 1 ?
            $this->mergeAudioFiles($audioFilePaths) :
            Storage::temporaryUrl($audioFilePaths[0], now()->addMinutes(15));

        $assembly = $this->assemblyAIFactory->make();
        $assembly->transcribe($tempUrl, $params);
    }

    public function getTranscriptionSubtitles(string $transcriptionId)
    {
        $assembly = $this->assemblyAIFactory->make();
        $subtitles = $assembly->getTranscriptionSubtitles($transcriptionId);
        $vttPath = 'subtitles/' . Str::uuid() . '.vtt';
        $srtPath = 'subtitles/' . Str::uuid() . '.srt';
        Storage::disk('s3')->put($vttPath, $subtitles['vtt']);
        Storage::disk('s3')->put($srtPath, $subtitles['srt']);

        return [
            'srt_file_path' => $srtPath,
            'vtt_file_path' => $vttPath
        ];
    }

    public function getTranscription(string $transcriptionId)
    {
        $assembly = $this->assemblyAIFactory->make();
        return $assembly->getTranscription($transcriptionId);
    }

    protected static function compressAndStoreMp3($localFile, $fileName)
    {
        $maxFileSize = 22 * 1024 * 1024; // 25 MB in bytes

        // Check if the file size is greater than 25 MB
        if ($localFile->getSize() > $maxFileSize) {
            // Compress the file using FFmpeg
            $inputPath = $localFile->getRealPath();
            $outputPath = Storage::disk('local')->path('compressed_' . $fileName);

            $process = new Process([
                'ffmpeg',
                '-i', $inputPath,
                '-codec:a', 'libmp3lame',
                '-b:a', '48k', // You can adjust the bitrate to control the output file size and quality
                $outputPath
            ]);

            $process->setTimeout(0);
            $process->run();

            // Check if the compression was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Replace the original file with the compressed one
            $newLocalFile = Storage::disk('local')->get('compressed_' . $fileName);

            // Store the file using the Storage facade
            Storage::disk('local')->put($fileName, $newLocalFile);
            return true;
        }

        return false;
    }
}
