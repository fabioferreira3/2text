<?php

namespace App\Listeners;

use App\Events\DocumentTaskFailed;
use App\Models\DocumentTask;

class HandleFailedDocumentTask
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
     * @param DocumentTaskFailed $event
     * @return void
     */
    public function handle(DocumentTaskFailed $event)
    {
        $tasksByProcess = DocumentTask::ofProcess($event->task->process_id)->inProgress()->except([$event->task->id])->get();
        $event->task->update(['status' => 'failed']);

        if (!$tasksByProcess->isEmpty()) {
            $tasksByProcess->each(function (DocumentTask $task) {
                $task->update(['status' => 'on_hold']);
            });
        }
    }
}
