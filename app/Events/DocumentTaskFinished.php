<?php

namespace App\Events;

use App\Models\DocumentTask;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentTaskFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentTask $task;
    public $completedTasksCount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $taskId, int $completedTasksCount = 0)
    {
        $this->task = DocumentTask::findOrFail($taskId);
        $this->completedTasksCount = $completedTasksCount;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('User.' . $this->task->document->getMeta('user_id'));
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->task->document->id,
            'task_id' => $this->task->id
        ];
    }

    public function broadcastAs()
    {
        return 'DocumentTaskFinished';
    }
}
