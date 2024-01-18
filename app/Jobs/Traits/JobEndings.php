<?php

namespace App\Jobs\Traits;

use App\Events\DocumentTaskFinished;
use App\Models\DocumentTask;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Support\Facades\Log;

trait JobEndings
{
    protected function jobSucceded($skipFinishedEvent = false)
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'finished']);
            if ($this->document && !$skipFinishedEvent) {
                $repo = new DocumentRepository($this->document);
                $completedTasksCount = $repo->increaseCompletedTasksCount();
                event(new DocumentTaskFinished($this->meta['task_id'], $completedTasksCount));
            }
        }
    }

    protected function jobPending()
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'pending']);
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

        //throw new Exception($errorMsg);
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

    protected function handleError(Exception $e, $customErrorMsg)
    {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            if ($e->getStatusCode() == 504) {
                Log::error('Timeout (504): ' . $e->getMessage());
            } else {
                $this->jobFailed("{$customErrorMsg}: " . $e->getMessage());
            }
        } else {
            Log::error($e->getMessage());
            $this->jobAborted();
        }
    }
}
