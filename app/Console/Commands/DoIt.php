<?php

namespace App\Console\Commands;

use Anthropic\Laravel\Facades\Anthropic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * @codeCoverageIgnore
 */
class DoIt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    // 'claude-3-opus-20240229'
    // 'claude-3-sonnet-20240229'

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = Anthropic::messages()->create([
            'model' => 'claude-3-sonnet-20240229',
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => 'Hello! My name is Fabio.'],
            ],
        ]);
        Log::debug($result->role);
        Log::debug($result->stop_reason);
        Log::debug($result->usage->inputTokens);
        Log::debug($result->usage->outputTokens);

        $this->info($result->content[0]->text);
    }
}
