<?php

namespace App\Jobs;

use App\Enums\LanguageModels;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\Whisper\Whisper;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Get the file from S3 and store it locally if it doesn't exist
            if (!Storage::disk('local')->exists($this->document->meta['audio_file_path'])) {
                $fileUrl = Storage::disk('s3')->get($this->document->meta['audio_file_path']);
                Storage::disk('local')->put($this->document->meta['audio_file_path'], $fileUrl);
            }

            // Check the file size and compress it if necessary
            $localFilePath = Storage::disk('local')->path($this->document->meta['audio_file_path']);
            $neededToCompress = $this->compressAndStoreMp3(new \Illuminate\Http\File($localFilePath));

            // If the file was compressed, replace the local file with the compressed one
            // if ($neededToCompress) {
            //     Storage::disk('local')->delete($this->document->meta['audio_file_path']);
            //     $localFilePath = Storage::disk('local')->path($compressedFilePath);
            // }

            $whisper = new Whisper($localFilePath);
            $response = $whisper->request();

            // Clean up the local files
            Storage::disk('local')->delete($this->document->meta['audio_file_path']);
            if ($neededToCompress) {
                Storage::disk('local')->delete('compressed_' . $this->document->meta['audio_file_path']);
            }

            $this->document->update(['meta' => [...$this->document->meta, 'context' => $response['text'], 'original_text' => $response['text']]]);

            $repo = new DocumentRepository($this->document);
            $repo->addHistory(
                [
                    'field' => 'content',
                    'content' => $response['text']
                ],
                [
                    'model' => LanguageModels::WHISPER->value,
                    'length' => 0
                ]
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Audio download error: ' . $e->getMessage());
        }
    }

    protected function compressAndStoreMp3($file)
    {
        $maxFileSize = 25 * 1024 * 1024; // 25 MB in bytes

        // Check if the file size is greater than 25 MB
        if ($file->getSize() > $maxFileSize) {
            // Compress the file using FFmpeg
            $inputPath = $file->getRealPath();
            $outputPath = Storage::disk('local')->path('compressed_' . $this->document->meta['audio_file_path']);

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
            $file = Storage::disk('local')->get('compressed_' . $this->document->meta['audio_file_path']);

            // Store the file using the Storage facade
            Storage::disk('local')->put($this->document->meta['audio_file_path'], $file);
            return true;
        }

        return false;
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'process_audio_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
