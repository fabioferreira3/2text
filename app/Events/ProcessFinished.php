<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $userId;
    public string $documentId;
    public string $processId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->userId = $params['user_id'];
        $this->documentId = $params['document_id'];
        $this->processId = $params['process_id'];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'document_id' => $this->documentId,
            'process_id' => $this->processId
        ];
    }

    public function broadcastAs()
    {
        return 'ProcessFinished';
    }
}
