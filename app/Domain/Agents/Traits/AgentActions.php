<?php

namespace App\Domain\Agents\Traits;

use App\Domain\Agents\Jobs\PollRun;
use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\Thread;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;

trait AgentActions
{
    public function run(Thread $thread, array $metadata = [])
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

        PollRun::dispatch($threadRun, $metadata)->delay(now()->addSeconds(5));
    }
}
