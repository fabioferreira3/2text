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

class SummarizeText implements ShouldQueue
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
        $sentences = collect(preg_split("/(?<=[.?!])\s+(?=([^\d\w]*[A-Z][^.?!]+))/", $this->textRequest->original_text, -1, PREG_SPLIT_NO_EMPTY));
        $chunks = collect([]);

        $sentences->chunk(10)->each(function ($chunk) use ($chunks) {
            $chunks->push($chunk);
        });
        $results = collect([]);
        foreach ($chunks as $chunk) {
            $result = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => "Summarize the following text: " . $chunk->join(' '),
                'temperature' => 0.7,
                'max_tokens' => 100,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 1
            ]);
            $results->push($result['choices'][0]['text']);
        }
        $this->textRequest->update(['summary' => Str::squish($results->join(' '))]);

        GenerateTitle::dispatch($this->textRequest);
        GenerateMetaDescription::dispatch($this->textRequest);
    }
}
