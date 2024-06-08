<?php

namespace App\Domain\Agents\Jobs;

use App\Domain\Agents\Exceptions\SyncMessagesException;
use App\Domain\Agents\Events\ThreadMessagesReceived;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetrieveMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ThreadRun $threadRun;

    public function __construct(ThreadRun $threadRun)
    {
        $this->threadRun = $threadRun;
    }

    public function handle()
    {
        try {
            $assistant = new Assistant();
            $retrieveMessages = $assistant->retrieveThreadMessages(
                $this->threadRun->thread->external_id,
                $this->threadRun->run_id
            );
            if (count($retrieveMessages)) {
                foreach ($retrieveMessages as $message) {
                    $existingMessage = $this->threadRun->thread->messages()->where('external_id', $message['id'])->first();

                    if ($existingMessage) {
                        continue;
                    }

                    $creationDt = Carbon::createFromTimestamp($message['created_at'])->format('Y-m-d H:i:s');
                    $this->threadRun->thread->messages()->create([
                        'external_id' => $message['id'],
                        'content' => [
                            'text' => $message['content']
                        ],
                        'role' => $message['role'],
                        'created_at' => $creationDt,
                        'updated_at' => $creationDt
                    ]);
                }
                ThreadMessagesReceived::dispatch($this->threadRun->thread);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new SyncMessagesException($e->getMessage());
        }
    }
}
