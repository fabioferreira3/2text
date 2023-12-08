<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TitleGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Document $document;
    public string $processId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, $processId)
    {
        $this->document = $document;
        $this->processId = $processId;
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
            'process_id' => $this->processId
        ];
    }

    public function broadcastAs()
    {
        return 'TitleGenerated';
    }
}
