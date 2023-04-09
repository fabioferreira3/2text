<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProcessRequestFromUrl implements ShouldQueue, ShouldBeUnique
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
        $this->textRequest->update(['status' => 'processing']);
        $textRequest = $this->textRequest;

        Bus::chain([
            new DownloadAudio($textRequest),
            new ProcessAudio($textRequest),
            new BloggifyText($textRequest),
            new FinalizeProcess($textRequest)
        ])->dispatch();
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'process_url_request_' . $this->textRequest->id;
    }
}
