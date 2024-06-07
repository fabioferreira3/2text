<?php

namespace App\Console\Commands;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Factories\AgentFactory;
use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\Thread;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $factory = new AgentFactory();
        // $agent = $factory->make(Agent::THE_PARAPHRASER);

        // $threadParams = [
        //     'assistant_id' => $agent->resource['id'],
        //     'thread' => [
        //         'messages' => [
        //             [
        //                 'role' => 'user',
        //                 'content' => 'Wolverine é o típico anti-herói que emergiu na cultura popular americana após a Guerra do
        //                 Vietnã;[3] sua disposição para usar a força mortal e sua natureza ensanguentada tornaram-se características
        //                 fundamentais para outros anti-heróis dos quadrinhos no final da década de 1980.[4] Como resultado, o
        //                 personagem tornou-se um dos favoritos dos fãs da franquia X-Men[5] e teve sua primeira revista em 1988.'
        //             ]
        //         ]
        //     ]
        // ];
        $assistant = new Assistant();
        // $runRequest = $assistant->createAndRunThread($threadParams);
        // $thread = Thread::factory()->create([
        //     'external_id' => $runRequest->threadId
        // ]);
        // Log::debug($runRequest->toArray());
        // $threadRun = ThreadRun::create([
        //     'thread_id' => $thread->id,
        //     'assistant_id' => $agent->resource['id'],
        //     'run_id' => $runRequest->id,
        //     'status' => RunStatus::from($runRequest->status),
        //     'completed_at' => $runRequest->completedAt,
        //     'failed_at' => $runRequest->failedAt,
        //     'cancelled_at' => $runRequest->cancelledAt
        // ]);

        // Retrieve Run
        $retrieveRequest = $assistant->retrieveRun('thread_wpuxzXauAmgtlCqbsuiQTWyQ', 'run_8RaCUdM3paL32UmlW7gtupZd');
        dd($retrieveRequest->toArray());
    }
}
