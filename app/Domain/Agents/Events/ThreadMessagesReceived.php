<?php

namespace App\Domain\Agents\Events;

use App\Domain\Thread\Thread;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadMessagesReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Thread $thread;
    public array $metadata;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, array $metadata = [])
    {
        $this->thread = $thread;
        $this->metadata = $metadata;
    }
}
