<?php

namespace App\Jobs;

use App\Events\AudioProcessed;
use App\Models\TextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $filePath;
    public string $fileName;
    public string $language;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $fileName, string $language)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->language = $language;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::timeout(900)
            ->attach('audio_file', file_get_contents($this->filePath), $this->fileName)
            ->post("http://whisper:9000/asr?task=transcribe&language=$this->language&output=json");

        if ($response->failed()) {
            return $response->throw();
        }

        if ($response->successful()) {
            $textRequest = TextRequest::create(['original_text' => $response->json('text')]);
            event(new AudioProcessed($textRequest));
        }
    }
}
