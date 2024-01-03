<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RemoveEmbeddings;
use App\Models\Document;
use App\Models\DocumentTask;
use Carbon\Carbon;

describe('RemoveEmbeddings job', function () {
    it('handles success', function () {
        $document = Document::factory()->create();
        $task = DocumentTask::factory()->create(['document_id' => $document->id]);
        $job = new RemoveEmbeddings($document, ['collection_name' => 'test', 'task_id' => $task->id]);
        expect($job->retryUntil())->toBeInstanceOf(Carbon::class);
        $job->handle();

        expect($task->fresh()->status)->toBe('finished');
    });
});
