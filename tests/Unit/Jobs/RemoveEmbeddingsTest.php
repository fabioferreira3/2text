<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RemoveEmbeddings;
use App\Models\Document;
use App\Models\DocumentTask;
use Carbon\Carbon;

beforeEach(function () {
    $this->document = Document::factory()->create();
});

describe('RemoveEmbeddings job', function () {
    it('can be serialized', function () {
        $job = new RemoveEmbeddings($this->document, [
            'collection_name' => 'test'
        ]);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('handles success', function () {
        $task = DocumentTask::factory()->create(['document_id' => $this->document->id]);
        $job = new RemoveEmbeddings($this->document, ['collection_name' => 'test', 'task_id' => $task->id]);
        expect($job->retryUntil())->toBeInstanceOf(Carbon::class);
        $job->handle();

        expect($task->fresh()->status)->toBe('finished');
    });
});
