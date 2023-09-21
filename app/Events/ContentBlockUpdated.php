<?php

namespace App\Events;

use App\Models\DocumentContentBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentBlockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentContentBlock $contentBlock;
    public string $processId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DocumentContentBlock $contentBlock, string $processId)
    {
        $this->contentBlock = $contentBlock;
        $this->processId = $processId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->contentBlock->document->getMeta('user_id'));
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'document_content_block_id' => $this->contentBlock->id,
            'process_id' => $this->processId
        ];
    }

    public function broadcastAs()
    {
        return 'ContentBlockUpdated';
    }
}
