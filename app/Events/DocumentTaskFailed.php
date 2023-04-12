<?php

namespace App\Events;

use App\Models\DocumentTask;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentTaskFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentTask $task;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $taskId)
    {
        $this->task = DocumentTask::findOrFail($taskId);
    }
}
