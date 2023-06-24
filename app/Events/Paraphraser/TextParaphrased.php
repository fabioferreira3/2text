<?php

namespace App\Events\Paraphraser;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TextParaphrased implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'TextParaphrased';
    }
}
