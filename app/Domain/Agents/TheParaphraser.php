<?php

namespace App\Domain\Agents;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Interfaces\AgentInterface;
use App\Domain\Agents\Jobs\PollRun;
use App\Domain\Agents\Resources\AgentResource;
use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\Thread;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;

class TheParaphraser implements AgentInterface
{
    public $resource;

    public function __construct()
    {
        $this->resource = (new AgentResource(Agent::THE_PARAPHRASER))->toArray();
    }

    public function run(Thread $thread)
    {
        $assistant = new Assistant();

        $runRequest = $assistant->createRun($thread->external_id, $this->resource['id']);
        $threadRun = ThreadRun::create([
            'thread_id' => $thread->id,
            'assistant_id' => $this->resource['id'],
            'run_id' => $runRequest->id,
            'status' => RunStatus::from($runRequest->status),
            'completed_at' => $runRequest->completedAt,
            'failed_at' => $runRequest->failedAt,
            'cancelled_at' => $runRequest->cancelledAt
        ]);

        PollRun::dispatch($threadRun)->delay(now()->addSeconds(5));
    }
}
