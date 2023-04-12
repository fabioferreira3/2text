<?php

namespace App\Listeners;

use App\Events\DocumentTaskFinished;

class HandleFinishedDocumentTask
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
     * @param DocumentTaskFinished $event
     * @return void
     */
    public function handle(DocumentTaskFinished $event)
    {
        $event->task->update(['status' => 'finished']);
    }
}
