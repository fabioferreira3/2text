<?php

namespace App\Jobs;

use App\Models\DocumentTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class DispatchDocumentTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $readyDocs = DocumentTask::ready()->priorityFirst()->get();
        $jobsChain = [];
        foreach ($readyDocs as $task) {
            $class = $task->job;
            $jobsChain[] = new $class($task->document, [...$task->meta, 'task_id' => $task->id]);
        }

        Bus::chain($jobsChain)->dispatch();
    }
}
