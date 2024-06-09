<?php

namespace App\Domain\Agents\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $metadata;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->metadata['user_id']);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->metadata['document_id']
        ];
    }

    public function broadcastAs()
    {
        return 'TaskFailed';
    }
}
