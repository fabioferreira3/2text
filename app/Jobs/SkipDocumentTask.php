<?php

namespace App\Jobs;

use App\Models\DocumentTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SkipDocumentTask implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected DocumentTask $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $documentTaskId)
    {
        $this->task = DocumentTask::findOrFail($documentTaskId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->task->update(['status' => 'skipped']);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'skipping_task_' . $this->task->id;
    }
}
