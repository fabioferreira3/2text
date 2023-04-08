<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProcessRequestFromUrl
{
    use Dispatchable, SerializesModels;

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
        $this->textRequest->update(['status' => 'processing']);
        $textRequest = $this->textRequest;

        Bus::chain([
            new DownloadAudio($textRequest->refresh()),
            new ProcessAudio($textRequest->refresh()),
            new BloggifyText($textRequest->refresh()),
            function () use ($textRequest) {
                $textRequest->refresh();
                $textRequest->update(['status' => 'finished']);
            }
        ])->dispatch();
    }
}
