<?php

namespace App\Jobs\Traits;

use App\Models\DocumentTask;
use Exception;

trait JobEndings
{
    protected function jobSucceded()
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'finished']);
        }
    }

    protected function jobSkipped()
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'skipped']);
        }
    }

    protected function jobFailedButSkipped($errorMsg = '')
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'skipped']);
        }

        throw new Exception($errorMsg);
    }

    protected function jobFailed($errorMsg = '')
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $tasksByProcess = DocumentTask::ofProcess($task->process_id)->inProgress()->except([$task->id])->get();
            $task->update(['status' => 'failed']);

            if (!$tasksByProcess->isEmpty()) {
                $tasksByProcess->each(function (DocumentTask $taskProcess) {
                    $taskProcess->update(['status' => 'on_hold']);
                });
            }
        }

        throw new Exception($errorMsg);
    }

    protected function jobAborted($errorMsg = '')
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $tasksByProcess = DocumentTask::ofProcess($task->process_id)->inProgress()->except([$task->id])->get();
            $task->update(['status' => 'aborted']);

            if (!$tasksByProcess->isEmpty()) {
                $tasksByProcess->each(function (DocumentTask $taskProcess) {
                    $taskProcess->update(['status' => 'aborted']);
                });
            }
        }

        throw new Exception($errorMsg);
    }
}
