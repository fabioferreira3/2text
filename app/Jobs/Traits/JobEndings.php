<?php

namespace App\Jobs\Traits;

use App\Events\DocumentTaskAborted;
use App\Events\DocumentTaskFailed;
use App\Events\DocumentTaskFinished;
use App\Jobs\SkipDocumentTask;
use Exception;

trait JobEndings
{
    protected function jobSucceded()
    {
        if (isset($this->meta['task_id'])) {
            DocumentTaskFinished::dispatch($this->meta['task_id']);
        }
    }

    protected function jobSkipped()
    {
        if (isset($this->meta['task_id'])) {
            SkipDocumentTask::dispatch($this->meta['task_id']);
        }
    }

    protected function jobFailed($errorMsg = '')
    {
        if (isset($this->meta['task_id'])) {
            DocumentTaskFailed::dispatch($this->meta['task_id']);
        }

        throw new Exception($errorMsg);
    }

    protected function jobAborted($errorMsg = '')
    {
        if (isset($this->meta['task_id'])) {
            DocumentTaskAborted::dispatch($this->meta['task_id']);
        }

        throw new Exception($errorMsg);
    }
}
