<?php

namespace App\Jobs;

use App\Models\TextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateTitle implements ShouldQueue
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
        $text = $this->textRequest->summary;

        $result = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => "Write a title for the following text: $text",
            'temperature' => 0.5,
            'max_tokens' => 100
        ]);

        $title = Str::squish($result['choices'][0]['text'], '');
        $this->textRequest->update(['title' => $title]);
    }
}
