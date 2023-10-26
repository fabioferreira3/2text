<?php

namespace App\Events;

use App\Models\ChatThreadIteration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $iteration;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ChatThreadIteration $iteration)
    {
        $this->iteration = $iteration;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->iteration->thread->user_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'chat_thread_id' => $this->iteration->thread->id,
            'message' => $this->iteration->response
        ];
    }

    public function broadcastAs()
    {
        return 'ChatMessageReceived';
    }
}
