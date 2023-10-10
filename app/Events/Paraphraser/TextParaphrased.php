<?php

namespace App\Events\Paraphraser;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TextParaphrased implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Document $document;
    public array $params;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->params['user_id']);
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
            'process_id' => $this->params['process_id'],
            'user_id' => $this->params['user_id']
        ];
    }

    public function broadcastAs()
    {
        return 'TextParaphrased';
    }
}
