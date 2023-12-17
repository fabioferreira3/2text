<?php

namespace App\Jobs\Oraculum;

use App\Events\ChatMessageReceived;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Traits\JobEndings;
use App\Models\ChatThreadIteration;
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
    protected string $collectionName;
    public OraculumFactoryInterface $oraculumFactory;

    public function __construct(ChatThreadIteration $iteration, string $collectionName)
    {
        $this->iteration = $iteration;
        $this->collectionName = $collectionName;
        $this->oraculumFactory = app(OraculumFactoryInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = $this->oraculumFactory->make($this->iteration->thread->user, $this->collectionName);
            $response = $client->chat($this->iteration->response);
            $newIteration = $this->iteration->thread->iterations()->create([
                'response' => $response['content'],
                'origin' => 'sys'
            ]);
            event(new ChatMessageReceived($newIteration));
            $this->jobSucceded(true);
        } catch (Exception $e) {
            $this->jobFailed('Failed to ask question to Oraculum: ' . $e->getMessage());
        }
    }
}
