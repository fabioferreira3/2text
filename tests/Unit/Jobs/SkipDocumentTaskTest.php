<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\SkipDocumentTask;
use App\Models\DocumentTask;

beforeEach(function () {
    $this->documentTask = DocumentTask::factory()->create(['name' => DocumentTaskEnum::CREATE_TITLE]);
});

describe(
    'SkipDocumentTask job',
    function () {
        it('can be serialized', function () {
            $job = new SkipDocumentTask($this->documentTask->id);
            $serialized = serialize($job);
            expect($serialized)->toBeString();
        });

        it('skips a document task', function () {
            $job = new SkipDocumentTask($this->documentTask->id);

            $job->handle();

            $this->documentTask->refresh();
            expect($this->documentTask->status)->toEqual('skipped');
        });

        it('has a unique id', function () {
            $job = new SkipDocumentTask($this->documentTask->id);
            expect($job->uniqueId())->toEqual('skipping_task_' . $this->documentTask->id);
        });
    }
);
