<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestartRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pendingRequests = TextRequest::pending()->orderBy('created_at', 'ASC')->get();
        if ($pendingRequests->count()) {
            foreach ($pendingRequests as $textRequest) {
                ProcessRequestFromUrl::dispatchIf($textRequest->source_url && !$textRequest->audio_file_path, $textRequest);
                ProcessRequestFromAudio::dispatchIf($textRequest->audio_file_path, $textRequest);
            }
        }
    }
}
