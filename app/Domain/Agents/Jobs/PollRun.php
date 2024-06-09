<?php

namespace App\Domain\Agents\Jobs;

use App\Domain\Agents\Exceptions\PollRunException;
use App\Domain\Agents\Events\RunFailed;
use App\Domain\Agents\Events\TaskFailed;
use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class PollRun implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ThreadRun $threadRun;
    public array $metadata;

    public function __construct(ThreadRun $threadRun, array $metadata = [])
    {
        $this->threadRun = $threadRun;
        $this->metadata = $metadata;
    }

    public function handle()
    {
        try {
            $assistant = new Assistant();
            $request = $assistant->retrieveRun(
                $this->threadRun->thread->external_id,
                $this->threadRun->run_id
            );
            $this->threadRun->update([
                'status' => $request->status
            ]);
            switch ($request->status) {
                case RunStatus::QUEUED->value:
                case RunStatus::IN_PROGRESS->value:
                    self::dispatch($this->threadRun)->delay(now()->addSeconds(5));
                    break;
                case RunStatus::COMPLETED->value:
                    RetrieveMessages::dispatch($this->threadRun, $this->metadata);
                    break;
                case RunStatus::INCOMPLETE->value:
                case RunStatus::FAILED->value:
                case RunStatus::EXPIRED->value:
                case RunStatus::CANCELLED->value:
                case RunStatus::CANCELLING->value:
                    event(new RunFailed($this->threadRun, $request));
                    event(new TaskFailed($this->metadata));
                    break;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new PollRunException($e->getMessage());
        }
    }
}
