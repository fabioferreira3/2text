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

class GenerateMetaDescription implements ShouldQueue
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
            'prompt' => "Write a meta description, using no more than 15 words, about the text below. Text: '$text'",
            'temperature' => 0.2,
            'max_tokens' => 50
        ]);

        $metaDescription = Str::squish($result['choices'][0]['text'], '');
        $this->textRequest->update(['meta_description' => $metaDescription]);
    }
}
