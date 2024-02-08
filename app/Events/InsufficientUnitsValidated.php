<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InsufficientUnitsValidated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Document $document;
    public string $taskName;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, string $taskName)
    {
        $this->document = $document;
        $this->taskName = $taskName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->document->getMeta('user_id'));
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->document->id,
            'task' => $this->taskName
        ];
    }

    public function broadcastAs()
    {
        return 'InsufficientUnitsValidated';
    }
}
