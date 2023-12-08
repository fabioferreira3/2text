<?php

namespace App\Jobs;

use App\Models\Document;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProcessAudioInternally implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->document->getMeta('context')) {
            return;
        }

        $fileName = Str::uuid() . '.mp3';
        $processer = $this->defineProcesser();
        $response = Http::timeout(900)
            ->attach('audio_file', file_get_contents($this->document->getMeta('audio_file_path')), $fileName)
            ->post($processer);

        if ($response->failed()) {
            return $response->throw();
        }

        if ($response->successful()) {
            $originalText = Str::squish($response->json('text'));
            $this->document->update(['context' => $originalText]);
        } else {
            throw new Exception('Unable to process audio on Whisper');
        }
    }

    public function defineProcesser()
    {
        $language = $this->document->language;
        $service = 'whisper';

        if ($language != 'en') {
            $service = 'whisper-large';
        }

        return "http://$service:9000/asr?task=transcribe&language=$language&output=json";
    }
}
