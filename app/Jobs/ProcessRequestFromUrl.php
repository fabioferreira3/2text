<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;

class ProcessRequestFromUrl
{
    use Dispatchable;

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
            new DownloadAudio($textRequest),
            new ProcessAudio($textRequest),
            new BloggifyText($textRequest),
            new FinalizeProcess($textRequest)
        ])->dispatch();
    }
}
