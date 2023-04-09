<?php

namespace App\Jobs;

use App\Models\TextRequest;
use App\Packages\Whisper\Whisper;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TextRequest $textRequest;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TextRequest $textRequest)
    {
        $this->textRequest = $textRequest->fresh();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // public function handle()
    // {
    //     if ($this->textRequest->original_text) {
    //         return;
    //     }

    //     $localFilePath = Storage::disk('local')->path($this->textRequest->audio_file_path);

    //     if (!Storage::disk('local')->exists($this->textRequest->audio_file_path)) {
    //         $fileUrl = Storage::disk('s3')->get($this->textRequest->audio_file_path);
    //         Storage::disk('local')->put($this->textRequest->audio_file_path, $fileUrl);
    //     }

    //     $whisper = new Whisper($localFilePath);
    //     $response = $whisper->request();

    //     Storage::disk('local')->delete($this->textRequest->audio_file_path);

    //     $this->textRequest->update(['original_text' => $response['text']]);
    // }
    protected function compressAndStoreMp3($file)
    {
        $maxFileSize = 25 * 1024 * 1024; // 25 MB in bytes

        // Check if the file size is greater than 25 MB
        if ($file->getSize() > $maxFileSize) {
            Log::debug('is bigger');
            // Compress the file using FFmpeg
            $inputPath = $file->getRealPath();
            $outputPath = Storage::disk('local')->path('compressed_' . $this->textRequest->audio_file_path);
            //  $outputPath = tempnam(sys_get_temp_dir(), 'compressed_') . '.mp3';
            Log::debug($outputPath);

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
            //$file = new \Illuminate\Http\File($outputPath);
            $file = Storage::disk('local')->get('compressed_' . $this->textRequest->audio_file_path);

            // Store the file using the Storage facade
            Storage::disk('local')->put($this->textRequest->audio_file_path, $file);
            return true;
        }

        return false;
    }

    public function handle()
    {
        if ($this->textRequest->original_text) {
            return;
        }

        // Get the file from S3 and store it locally if it doesn't exist
        if (!Storage::disk('local')->exists($this->textRequest->audio_file_path)) {
            $fileUrl = Storage::disk('s3')->get($this->textRequest->audio_file_path);
            Storage::disk('local')->put($this->textRequest->audio_file_path, $fileUrl);
        }

        // Check the file size and compress it if necessary
        $localFilePath = Storage::disk('local')->path($this->textRequest->audio_file_path);
        $neededToCompress = $this->compressAndStoreMp3(new \Illuminate\Http\File($localFilePath));

        // If the file was compressed, replace the local file with the compressed one
        // if ($neededToCompress) {
        //     Storage::disk('local')->delete($this->textRequest->audio_file_path);
        //     $localFilePath = Storage::disk('local')->path($compressedFilePath);
        // }

        $whisper = new Whisper($localFilePath);
        $response = $whisper->request();

        // Clean up the local files
        // Storage::disk('local')->delete($this->textRequest->audio_file_path);
        // if ($compressedFilePath !== $localFilePath) {
        //     Storage::disk('local')->delete($compressedFilePath);
        // }

        $this->textRequest->update(['original_text' => $response['text']]);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'process_audio_' . $this->textRequest->id;
    }
}
