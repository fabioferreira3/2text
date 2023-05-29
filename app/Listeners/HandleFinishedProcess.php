<?php

namespace App\Listeners;

use App\Enums\DocumentType;
use App\Events\DocumentTaskFinished;
use App\Models\DocumentTask;
use App\Repositories\DocumentRepository;

class HandleFinishedProcess
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
        if ($event->task->document->type !== DocumentType::BLOG_POST) {
            return;
        }

        $tasksByProcess = DocumentTask::ofProcess($event->task->process_id)->get();
        $finishedCount = $tasksByProcess->where('status', 'finished')->count();

        if ($tasksByProcess->count() === $finishedCount) {
            $repo = new DocumentRepository($event->task->document);
            $repo->publishText();
        }
    }
}
