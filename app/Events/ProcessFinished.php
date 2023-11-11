<?php

namespace App\Events;

use App\Models\DocumentTask;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentTask $documentTask;
    public bool $groupFinished;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $processId)
    {
        $this->documentTask = DocumentTask::ofProcess($processId)->firstOrFail();
        $this->groupFinished = false;

        if ($this->documentTask->process_group_id) {
            $processGroupTasksCount = $this->documentTask->siblings()->count();
            $processGroupTasksFinishedCount = $this->documentTask->siblings()->completed()->count();
            $this->groupFinished = $processGroupTasksCount === $processGroupTasksFinishedCount;
        }
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
            'has_siblings' => $this->documentTask->siblings()->count() > 0,
            'process_group_id' => $this->documentTask->process_group_id ?? null,
            'group_finished' => $this->groupFinished
        ];
    }

    public function broadcastAs()
    {
        return 'ProcessFinished';
    }
}
