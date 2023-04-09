<?php

namespace App\Jobs;

use App\Models\TextRequest;
use App\Packages\Whisper\Whisper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $this->textRequest = $textRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $localFilePath = Storage::disk('local')->path($this->textRequest->audio_file_path);

        if (!Storage::disk('local')->exists($this->textRequest->audio_file_path)) {
            $fileUrl = Storage::disk('s3')->get($this->textRequest->audio_file_path);
            Storage::disk('local')->put($this->textRequest->audio_file_path, $fileUrl);
        }

        $whisper = new Whisper($localFilePath);
        $response = $whisper->request();

        Storage::disk('local')->delete($this->textRequest->audio_file_path);

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
