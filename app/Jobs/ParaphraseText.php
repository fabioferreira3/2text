<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ParaphraseText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TextRequest $textRequest;

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
        $response = Http::acceptJson()->post('https://wai.wordai.com/api/rewrite', [
            'email' => 'fabio86ferreira@gmail.com',
            'key' => env('WORDAI_API_KEY'),
            'input' => $this->textRequest->original_text,
            'rewrite_num' => 1,
            'uniqueness' => 1,
            'return_rewrites' => true
        ]);

        if ($response->successful()) {
            //    $this->textRequest->update(['paraphrased_text' => $response->json('rewrites')[0]]);
        } else {
            return $response->throw();
        }
    }
}
