<?php

namespace App\Jobs\Oraculum;

use App\Events\ChatMessageReceived;
use App\Jobs\Traits\JobEndings;
use App\Models\ChatThreadIteration;
use App\Packages\Oraculum\Oraculum;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class Ask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected ChatThreadIteration $iteration;
    protected string $taskId;

    public function __construct(ChatThreadIteration $iteration, string $taskId)
    {
        $this->iteration = $iteration;
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new Oraculum($this->iteration->thread->user, $this->taskId);
            $response = $client->chat($this->iteration->response);
            $newIteration = $this->iteration->thread->iterations()->create([
                'response' => $response['data'],
                'origin' => 'sys'
            ]);
            event(new ChatMessageReceived($newIteration));
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create title: ' . $e->getMessage());
        }
    }
}
