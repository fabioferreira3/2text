<?php

namespace App\Events;

use App\Models\DocumentTask;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImageNotGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentTask $documentTask;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $processId)
    {
        $this->documentTask = DocumentTask::ofProcess($processId)->firstOrFail();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->documentTask->document->getMeta('user_id'));
    }

    public function broadcastWith()
    {
        return [
            'document_id' => $this->documentTask->document_id,
            'parent_document_id' => $this->documentTask->document->parent_document_id,
            'process_id' => $this->documentTask->process_id,
            'process_group_id' => $this->documentTask->process_group_id ?? null
        ];
    }

    public function broadcastAs()
    {
        return 'ImageNotGenerated';
    }
}
