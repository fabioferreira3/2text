<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class DispatchAllDocumentTasks implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;

    public function handle()
    {
        $tasks = DocumentTask::available()->priorityFirst()->get();

        if (!$tasks->count()) {
            return;
        }

        DB::table('document_tasks')->whereIn('id', $tasks->pluck('id')->toArray())->update(['status' => 'in_progress']);

        $tasksByProcess = $tasks->groupBy('process_id')->all();
        foreach ($tasksByProcess as $processId => $processTasks) {
            $jobsChain = [];
            foreach ($processTasks as $task) {
                $class = $task->job;
                $jobsChain[] = new $class($task->document, [
                    ...$task->meta,
                    'task_id' => $task->id,
                    'process_id' => $task->process_id,
                    'order' => $task->order,
                ]);
            }
            if (!empty($jobsChain)) {
                Bus::chain($jobsChain)->dispatch();
            }
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'dispatching_all_tasks';
    }
}
