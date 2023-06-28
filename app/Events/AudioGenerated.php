<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Document $document;
    public string $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, string $userId)
    {
        $this->document = $document;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->userId);
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
            'audio_file' => $this->document->meta['audio_file']
        ];
    }

    public function broadcastAs()
    {
        return 'AudioGenerated';
    }
}
