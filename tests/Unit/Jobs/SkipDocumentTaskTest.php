<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\SkipDocumentTask;
use App\Models\DocumentTask;

describe(
    'SkipDocumentTask job',
    function () {
        it('skips a document task', function () {
            $documentTask = DocumentTask::factory()->create(['status' => DocumentTaskEnum::CREATE_TITLE]);
            $job = new SkipDocumentTask($documentTask->id);

            $job->handle();

            $documentTask->refresh();
            expect($documentTask->status)->toEqual('skipped');
        });

        it('has a unique id', function () {
            // Prepare
            $documentTask = DocumentTask::factory()->create();
            $job = new SkipDocumentTask($documentTask->id);

            // Assert
            expect($job->uniqueId())->toEqual('skipping_task_' . $documentTask->id);
        });
    }
);
