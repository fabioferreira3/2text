<?php

namespace App\Listeners;

use App\Events\DocumentTaskAborted;
use App\Models\DocumentTask;

class HandleAbortedDocumentTask
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param DocumentTaskAborted $event
     * @return void
     */
    public function handle(DocumentTaskAborted $event)
    {
        $tasksByProcess = DocumentTask::ofProcess($event->task->process_id)
            ->inProgress()->except([$event->task->id])->get();
        $event->task->update(['status' => 'aborted']);

        if (!$tasksByProcess->isEmpty()) {
            $tasksByProcess->each(function (DocumentTask $task) {
                $task->update(['status' => 'aborted']);
            });
        }
    }
}
