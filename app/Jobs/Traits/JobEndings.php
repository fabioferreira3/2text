<?php

namespace App\Jobs\Traits;

use App\Events\DocumentTaskFinished;
use App\Models\DocumentTask;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

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

    protected function jobFailedButSkipped()
    {
        if (isset($this->meta['task_id'])) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $task->update(['status' => 'skipped']);
        }
    }

    protected function jobFailed()
    {
        if ($this->meta['task_id'] ?? false) {
            $task = DocumentTask::findOrFail($this->meta['task_id']);
            $tasksByProcess = DocumentTask::ofProcess($task->process_id)->inProgress()->except([$task->id])->get();
            $task->update(['status' => 'failed']);

            if (!$tasksByProcess->isEmpty()) {
                $tasksByProcess->each(function (DocumentTask $taskProcess) {
                    $taskProcess->update(['status' => 'on_hold']);
                });
            }
        }

        $this->fail();
    }

    protected function jobAborted($errorMsg = '')
    {
        Log::error($errorMsg);
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
        $this->delete();
    }

    protected function handleError(HttpException $e, $customErrorMsg)
    {
        if ($e->getStatusCode() == 504) {
            Log::error('Timeout (504): ' . $e->getMessage());
        } else {
            $this->jobFailed("{$customErrorMsg}: " . $e->getMessage());
        }
    }
}
