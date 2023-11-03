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

    public function calculateTasksProgress()
    {
        $totalTasks = $this->task->document->getMeta('total_tasks_count');
        return floor(($this->completedTasksCount * 100) / $totalTasks);
    }

    public function defineThought($index)
    {
        $thoughts = $this->task->document->getMeta('thoughts') ?? [];
        if (count($thoughts)) {
            return $thoughts[$index] ?? __('oraculum.hmmm');
        }

        return __('oraculum.where_to_start');
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
            'completed_tasks_count' => $this->completedTasksCount,
            'tasks_progress' => $this->calculateTasksProgress() . "%",
            'thought' => $this->defineThought($this->completedTasksCount - 1)
        ];
    }

    public function broadcastAs()
    {
        return 'DocumentTaskFinished';
    }
}
