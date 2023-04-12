<?php

namespace App\Listeners;

use App\Events\DocumentTaskFailed;

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
        $event->task->update(['status' => 'failed']);
    }
}
